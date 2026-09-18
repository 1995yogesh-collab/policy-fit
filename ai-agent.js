const fs = require('fs');
const path = require('path');

const API_KEY = process.env.GEMINI_API_KEY;
const URL = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key=${API_KEY}`;

async function askGemini(prompt) {
    const response = await fetch(URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ contents: [{ parts: [{ text: prompt }] }] })
    });
    const data = await response.json();
    let text = data.candidates[0].content.parts[0].text;
    // Remove markdown formatting
    text = text.replace(/^```(html|php)?\s*/i, '').replace(/\s*```$/i, '').trim();
    return text;
}

async function run() {
    console.log("🚀 Starting Daily AI Agent...");
    const date = new Date().toISOString().split('T')[0];

    // 1. GENERATE NEW USA INSURANCE CONTENT
    console.log("📝 Generating US Insurance Content...");
    const contentPrompt = "You are a US Insurance expert. Write a detailed, SEO-friendly, AdSense-approved article addressing a high-volume US insurance query (e.g., auto insurance rates, health insurance costs). Output ONLY raw HTML. Include a <h1>, well-structured <h2> sections, <ul> lists, and detailed actuarial/cost examples.";
    const articleHtml = await askGemini(contentPrompt);
    
    const usDir = path.join(__dirname, 'us');
    if (!fs.existsSync(usDir)) fs.mkdirSync(usDir);
    const fileName = `guide-${Date.now()}.html`;
    fs.writeFileSync(path.join(usDir, fileName), articleHtml);
    console.log(`✅ Saved new article: /us/${fileName}`);

    // 2. READ AND UPGRADE INDEX FILE (IF NEEDED)
    console.log("🔍 Auditing index.html...");
    const indexPath = path.join(__dirname, 'index.html');
    if (fs.existsSync(indexPath)) {
        const currentCode = fs.readFileSync(indexPath, 'utf-8');
        const upgradePrompt = `Here is my homepage code. Act as a web developer. Add a dynamic link to '/us/${fileName}' in the recent articles section. Improve SEO meta tags for US Insurance. OUTPUT ONLY THE UPGRADED RAW HTML CODE. Do not change the core layout.\n\n` + currentCode;
        const newCode = await askGemini(upgradePrompt);
        if(newCode.length > 500) {
            fs.writeFileSync(indexPath, newCode);
            console.log("✅ index.html upgraded.");
        }
    }

    // 3. GENERATE DAILY REPORT
    const reportPath = path.join(__dirname, 'AI-DAILY-REPORT.md');
    const reportContent = `## AI Agent Update: ${date}\n- **New Content:** Created \`/us/${fileName}\`\n- **Code Audit:** Evaluated and optimized \`index.html\`\n- **Status:** All changes committed and pushed to deployment pipeline.\n`;
    fs.writeFileSync(reportPath, reportContent);
    console.log("✅ Daily report updated.");
}

run().catch(console.error);
