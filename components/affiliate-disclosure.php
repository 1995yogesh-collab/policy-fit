<?php
// P03-W03: Contextual Affiliate Disclosure UX
// Renders a compliant, non-deceptive disclosure block for commercial pages.
?>
<style>
  .policy-disclosure-box {
    background-color: #f8f9fa;
    border-left: 4px solid #0056b3;
    padding: 12px 16px;
    margin: 20px 0;
    font-size: 0.85rem;
    line-height: 1.5;
    color: #495057;
    border-radius: 0 4px 4px 0;
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  }
  .policy-disclosure-box strong {
    color: #212529;
    display: block;
    margin-bottom: 4px;
    font-size: 0.9rem;
  }
  .policy-disclosure-box a {
    color: #0056b3;
    text-decoration: underline;
  }
  @media (max-width: 600px) {
    .policy-disclosure-box {
      font-size: 0.8rem;
      padding: 10px 12px;
    }
  }
</style>

<div class="policy-disclosure-box" role="note" aria-label="Affiliate Disclosure">
  <strong>Editorial Independence & Affiliate Disclosure</strong>
  Policy.fit provides independent insurance education. When you click links to partner products, we may earn a commission at no extra cost to you. This does not influence our editorial analysis. <a href="/privacy-policy">Learn how we make money</a>.
</div>
