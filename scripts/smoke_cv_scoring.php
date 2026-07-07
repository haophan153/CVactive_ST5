<?php
/**
 * Smoke test cho keyword extractor + skill matcher (local-only, không gọi OpenAI).
 * Chạy: php scripts/smoke_cv_scoring.php
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JobPost;
use App\Services\CvScoring\KeywordExtractor;
use App\Services\CvScoring\SkillMatcher;
use App\Services\CvScoring\AiScorer;
use App\Services\CvScoring\CvScoringService;

// --- Test 1: KeywordExtractor tiếng Việt ---
$jp = new JobPost([
    'title' => 'Lập trình viên ReactJS Senior',
    'category' => 'it',
    'company_name' => 'FPT Software',
    'description' => "Tuyển lập trình viên ReactJS có kinh nghiệm với TypeScript, Redux, GraphQL.
Yêu cầu: 3 năm kinh nghiệm, thành thạo JavaScript ES6, hiểu biết về AWS, Docker.
Có kinh nghiệm làm việc với Git, CI/CD là một lợi thế.",
]);

$extractor = app(KeywordExtractor::class);
$keywords = $extractor->extract($jp);

echo "=== Test 1: KeywordExtractor (tieng Viet) ===\n";
echo "So keywords: " . count($keywords) . "\n";
foreach ($keywords as $kw) {
    echo "  - key='{$kw['key']}' original='{$kw['original']}'\n";
}
$keyList = array_map(fn($k) => $k['key'], $keywords);
$expectContains = ['reactjs', 'typescript', 'redux', 'graphql', 'javascript', 'aws', 'docker'];
foreach ($expectContains as $need) {
    if (!in_array($need, $keyList, true)) {
        echo "FAIL: expected keyword '$need' missing\n";
        exit(1);
    }
}
echo "PASS\n\n";

// --- Test 2: SkillMatcher whole-word + không dấu ---
$matcher = app(SkillMatcher::class);
$cvText = "Nguyen Van A - Senior Frontend Developer
Ky nang: JavaScript, TypeScript, ReactJS, Redux, GraphQL, Node.js
Kinh nghiem: 4 nam lam viec voi AWS, Docker, Git
Lam viec voi CI/CD, Jenkins";

$result = $matcher->match($cvText, $keyList, $keywords);
echo "=== Test 2: SkillMatcher ===\n";
echo "match_ratio: {$result['match_ratio']}\n";
echo "matched: " . implode(', ', $result['matched']) . "\n";
echo "missing: " . implode(', ', $result['missing']) . "\n";
// CV của một React dev nên match được ít nhất 5/7 kỹ năng chính (reactjs, typescript, redux, graphql, javascript, aws, docker)
$coreKeywords = ['reactjs', 'typescript', 'redux', 'graphql', 'javascript', 'aws', 'docker'];
$coreMatched = array_intersect($coreKeywords, $result['matched']);
if (count($coreMatched) < 5) {
    echo "FAIL: chỉ match được " . count($coreMatched) . "/" . count($coreKeywords) . " core keywords\n";
    print_r($coreMatched);
    exit(1);
}
echo "PASS (core matched: " . count($coreMatched) . "/" . count($coreKeywords) . ")\n\n";

// --- Test 3: AiScorer::isConfigured() ---
$ai = app(AiScorer::class);
echo "=== Test 3: AiScorer config check ===\n";
echo "isConfigured: " . ($ai->isConfigured() ? 'true' : 'false') . "\n";
echo "model: " . config('services.openai.model') . "\n";
echo "PASS\n\n";

// --- Test 4: CvScoringService integration (local-only) ---
echo "=== Test 4: CvScoringService integration ===\n";
$jobApp = new \App\Models\JobApplication([
    'job_post_id' => 999,
    'full_name' => 'Nguyen Van A',
    'email' => 'a@example.com',
    'cv_text' => $cvText,
    'status' => 'pending',
]);
$jobApp->setRelation('jobPost', $jp);

$svc = app(CvScoringService::class);
$result = $svc->scoreAndStore($jobApp);
echo "score: {$result['score']}\n";
echo "summary: {$result['summary']}\n";
echo "source: " . ($result['breakdown']['source'] ?? '?') . "\n";
echo "matched_keywords: " . implode(', ', $result['breakdown']['matched_keywords'] ?? []) . "\n";
echo "missing_keywords: " . implode(', ', $result['breakdown']['missing_keywords'] ?? []) . "\n";

if ($result['score'] <= 0 || $result['score'] > 100) {
    echo "FAIL: score ngoài phạm vi\n";
    exit(1);
}
echo "PASS\n\n";

echo "All smoke tests passed.\n";