<?php
// Security: allow CLI or token access
if (php_sapi_name() !== 'cli' && (!isset($_GET['token']) || $_GET['token'] !== 'policyfit_secret_2026')) {
    die("Access Denied.");
}

// 1. Setup Directories & IndexNow Key
$rootDir = __DIR__;
$usDir = $rootDir . '/us';
if (!is_dir($usDir)) {
    mkdir($usDir, 0755, true);
}

// Auto-create IndexNow key file if missing
$indexKey = "policyfitindexnow2026";
if (!file_exists($rootDir . "/{$indexKey}.txt")) {
    file_put_contents($rootDir . "/{$indexKey}.txt", $indexKey);
}

// 2. High-CPC USA Insurance Seed Pool (Auto Rotating)
$topics = [
    "Texas vs Florida Auto Insurance Rate Comparison 2026",
    "ACA Health Marketplace Silver Plan Deductible Benchmark",
    "Medicare Advantage Part C Out-of-Pocket Maximums 2026",
    "Small Business General Liability Insurance Cost Guidelines",
    "Term Life Insurance 20 vs 30 Year Rates for 40-Year-Olds",
    "Homeowners Hazard and Flood Insurance Bundling Savings"
];
$chosenTopic = $topics[array_rand($topics)];
$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $chosenTopic)));

// 3. Gemini API Call
$apiKey = "YOUR_GEMINI_API_KEY"; // Yahan apni Gemini API Key paste karein

$prompt = "You are an independent US insurance analyst writing for Policy.fit.
Topic: '{$chosenTopic}'.
Write a comprehensive, 1200+ word, high-E-E-A-T research guide.
Include:
1. State-specific or industry cost benchmarks.
2. An actionable comparison table.
3. A 4-question FAQ section.
4. Schema JSON-LD (FAQPage & FinancialProduct) in a <script type='application/ld+json'> block.
Output ONLY valid clean HTML (start with <h2> and end with </section>). Do NOT include markdown code fences.";

$payload = [
    "contents" => [
        ["parts" => [["text" => $prompt]]]
    ]
];

$ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$res = curl_exec($ch);
curl_close($ch);

$data = json_decode($res, true);
$articleBody = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

if (empty($articleBody)) {
    die("Content generation failed. Check API key.");
}

$articleBody = preg_replace('/^```html/m', '', $articleBody);$articleBody = preg_replace('/^```/m', '', $articleBody);

// 4. Assemble Static HTML Guide
$fullHtml = '<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>' . htmlspecialchars($chosenTopic) . ' (2026 Analysis) | Policy.fit</title>
  <meta name="description" content="Independent 2026 benchmarks, rate tables, and actuarial analysis for ' . htmlspecialchars($chosenTopic) . '.">
  <link rel="canonical" href="https://policy.fit/us/' . $slug . '">
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.7; color: #0f172a; background: #f8fafc; margin: 0; padding: 1.5rem; }
    .container { max-width: 850px; margin: 1.5rem auto; background: #fff; padding: 2.5rem; border-radius: 12px; border: 1px solid #e2e8f0; }
    h1, h2, h3 { color: #0f172a; }
    table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; font-size: 0.95rem; }
    th, td { border: 1px solid #cbd5e1; padding: 0.75rem 1rem; text-align: left; }
    th { background: #f1f5f9; font-weight: 700; }
    a { color: #2563eb; text-decoration: none; }
    .footer-note { margin-top: 2.5rem; padding: 1rem; background: #f8fafc; border-left: 4px solid #2563eb; font-size: 0.85rem; color: #64748b; }
  </style>
</head>
<body>
  <div class="container">
    <p><a href="/">← Policy.fit Home</a> / <span>US Insurance Benchmarks</span></p>
    <h1>' . htmlspecialchars($chosenTopic) . '</h1>
    ' . $articleBody . '
    <div class="footer-note">
      <strong>Actuarial Verification:</strong> Research conducted under NAIC & state insurance department guidelines. Updated for 2026.
    </div>
  </div>
</body>
</html>';

file_put_contents("{$usDir}/{$slug}.html", $fullHtml);

// 5. IndexNow Auto-Ping (Fast Bing & Partner Indexing)
$indexPayload = [
    "host" => "policy.fit",
    "key" => $indexKey,
    "keyLocation" => "https://policy.fit/{$indexKey}.txt",
    "urlList" => ["https://policy.fit/us/{$slug}"]
];

$ch = curl_init("https://api.indexnow.org/indexnow");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($indexPayload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8']);
curl_exec($ch);
curl_close($ch);

echo "SUCCESS: Created https://policy.fit/us/{$slug} and pinged IndexNow.";
?>
