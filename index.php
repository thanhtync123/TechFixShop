<?php
session_start();
$isLoggedIn = isset($_SESSION['role']);
$role = $_SESSION['role'] ?? null;
$name = $_SESSION['name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="#0a0e27" />
  <title>TechFix - Giải pháp sửa chữa công nghệ toàn diện</title>
  <meta name="description" content="TechFix cung cấp dịch vụ sửa chữa và hỗ trợ kỹ thuật cho cá nhân và doanh nghiệp trên toàn quốc." />
  <meta name="keywords" content="sửa chữa máy tính, sửa laptop, cứu dữ liệu, hỗ trợ kỹ thuật, TechFix, camera giám sát" />
  <meta property="og:title" content="TechFix - Giải pháp sửa chữa công nghệ toàn diện" />
  <meta property="og:description" content="Dịch vụ sửa chữa và hỗ trợ kỹ thuật đa dịch vụ cho cá nhân và doanh nghiệp trên toàn quốc." />
  <meta property="og:type" content="website" />
  <meta property="og:image" content="https://images.unsplash.com/photo-1518770660439-4636190af475?w=1200&q=80" />

  <!-- PWA -->
  <link rel="manifest" href="/TechFixPHP/manifest.json">
  <link rel="apple-touch-icon" href="/TechFixPHP/assets/image/vlute2.png">

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    /* =========================================================
       DESIGN TOKENS
    ========================================================= */
    :root {
      --bg: #060816; --bg-2: #0a0e27;
      --surface: rgba(255,255,255,0.04); --surface-2: rgba(255,255,255,0.06);
      --border: rgba(255,255,255,0.10);
      --text: #eef1ff; --muted: #9aa3c7;
      --blue: #2563eb; --indigo: #4f46e5; --violet: #7c3aed; --cyan: #06b6d4; --sky: #38bdf8;
      --grad-main: linear-gradient(135deg,#2563eb 0%,#7c3aed 50%,#06b6d4 100%);
      --grad-soft: linear-gradient(135deg,#4f46e5 0%,#06b6d4 100%);
      --grad-text: linear-gradient(120deg,#38bdf8,#818cf8,#c084fc);
      --shadow-sm: 0 4px 20px rgba(2,6,23,0.4);
      --shadow-md: 0 12px 40px rgba(7,12,45,0.55);
      --shadow-glow: 0 0 50px rgba(79,70,229,0.35);
      --radius: 18px; --radius-lg: 26px; --max: 1240px; --nav-h: 76px;
    }

    /* RESET */
    * { margin:0; padding:0; box-sizing:border-box; }
    html { scroll-behavior:smooth; scroll-padding-top:var(--nav-h); }
    body { font-family:'Plus Jakarta Sans',system-ui,sans-serif; background:var(--bg); color:var(--text); line-height:1.65; overflow-x:hidden; -webkit-font-smoothing:antialiased; }
    h1,h2,h3,h4 { font-family:'Outfit',sans-serif; line-height:1.15; }
    a { color:inherit; text-decoration:none; }
    img { max-width:100%; display:block; }
    ul { list-style:none; }
    button { font-family:inherit; cursor:pointer; border:none; }
    section { position:relative; }

    /* LAYOUT */
    .container { width:100%; max-width:var(--max); margin:0 auto; padding:0 22px; }
    .section-pad { padding:110px 0; }
    .center { text-align:center; }
    .eyebrow { display:inline-flex; align-items:center; gap:8px; font-size:.82rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:var(--sky); padding:8px 16px; border-radius:999px; background:var(--surface); border:1px solid var(--border); backdrop-filter:blur(10px); margin-bottom:18px; }
    .section-title { font-size:clamp(2rem,4.5vw,3rem); font-weight:800; letter-spacing:-.5px; margin-bottom:16px; text-wrap:balance; }
    .section-sub { color:var(--muted); max-width:640px; margin:0 auto; font-size:1.05rem; }
    .gradient-text { background:var(--grad-text); -webkit-background-clip:text; background-clip:text; color:transparent; }
    .glass { background:var(--surface); border:1px solid var(--border); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px); box-shadow:var(--shadow-sm); }

    /* NỀN ĐỘNG */
    .bg-orbs { position:fixed; inset:0; z-index:-2; overflow:hidden; }
    .orb { position:absolute; border-radius:50%; filter:blur(90px); opacity:.45; animation:floatOrb 18s ease-in-out infinite; }
    .orb.b1 { width:480px; height:480px; background:#2563eb; top:-120px; left:-100px; }
    .orb.b2 { width:420px; height:420px; background:#7c3aed; top:30%; right:-120px; animation-delay:-6s; }
    .orb.b3 { width:380px; height:380px; background:#06b6d4; bottom:-120px; left:30%; animation-delay:-10s; }
    @keyframes floatOrb { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(40px,-50px) scale(1.12)} }
    .bg-grid { position:fixed; inset:0; z-index:-1; background-image:linear-gradient(rgba(99,102,241,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(99,102,241,.06) 1px,transparent 1px); background-size:60px 60px; mask-image:radial-gradient(ellipse at 50% 0%,#000 30%,transparent 80%); }
    .particles { position:fixed; inset:0; z-index:-1; overflow:hidden; pointer-events:none; }
    .particle { position:absolute; width:6px; height:6px; border-radius:50%; background:var(--sky); opacity:.5; bottom:-10px; animation:rise linear infinite; }
    @keyframes rise { 0%{transform:translateY(0) translateX(0);opacity:0} 10%{opacity:.6} 90%{opacity:.5} 100%{transform:translateY(-110vh) translateX(40px);opacity:0} }

    /* LOADING */
    #loader { position:fixed; inset:0; z-index:9999; background:radial-gradient(circle at 50% 40%,#0a0e27,#060816); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:26px; transition:opacity .6s ease,visibility .6s ease; }
    #loader.hidden { opacity:0; visibility:hidden; }
    .loader-ring { width:70px; height:70px; border-radius:50%; border:4px solid rgba(255,255,255,.12); border-top-color:var(--sky); border-right-color:var(--violet); animation:spin 1s linear infinite; }
    @keyframes spin { to{transform:rotate(360deg)} }
    .loader-brand { font-family:'Outfit'; font-weight:800; font-size:1.6rem; letter-spacing:1px; }
    .loader-brand span { background:var(--grad-text); -webkit-background-clip:text; background-clip:text; color:transparent; }

    /* BUTTONS */
    .btn { display:inline-flex; align-items:center; justify-content:center; gap:10px; padding:14px 28px; border-radius:999px; font-weight:700; font-size:.98rem; transition:transform .25s ease,box-shadow .25s ease; white-space:nowrap; }
    .btn-primary { background:var(--grad-main); color:#fff; box-shadow:var(--shadow-glow); }
    .btn-primary:hover { transform:translateY(-3px); box-shadow:0 10px 40px rgba(124,58,237,.55); }
    .btn-ghost { background:var(--surface); color:var(--text); border:1px solid var(--border); backdrop-filter:blur(10px); }
    .btn-ghost:hover { transform:translateY(-3px); background:var(--surface-2); border-color:var(--sky); }

    /* =========================================================
       NAVBAR — kết hợp search + auth từ file PHP cũ
    ========================================================= */
    header.nav { position:fixed; top:0; left:0; right:0; z-index:1000; height:var(--nav-h); display:flex; align-items:center; transition:.35s ease; }
    header.nav.scrolled { background:rgba(6,8,22,0.78); backdrop-filter:blur(18px); box-shadow:0 6px 30px rgba(0,0,0,.35); border-bottom:1px solid var(--border); }
    .nav-inner { display:flex; align-items:center; justify-content:space-between; width:100%; gap:20px; }
    .logo { display:flex; align-items:center; gap:10px; font-family:'Outfit'; font-weight:800; font-size:1.35rem; }
    .logo-mark { width:38px; height:38px; border-radius:11px; background:var(--grad-main); display:grid; place-items:center; box-shadow:var(--shadow-glow); color:#fff; font-size:1.1rem; }
    .logo span { background:var(--grad-text); -webkit-background-clip:text; background-clip:text; color:transparent; }

    /* Thanh tìm kiếm giọng nói */
    .nav-search { flex:1; max-width:400px; position:relative; display:none; }
    @media(min-width:1024px) { .nav-search { display:block; } }
    .search-box-wrapper { position:relative; width:100%; }
    .search-box-wrapper input { width:100%; padding:10px 45px 10px 18px; border-radius:99px; background:var(--surface); border:1px solid var(--border); color:#fff; font-family:'Plus Jakarta Sans',sans-serif; outline:none; transition:.3s; font-size:.9rem; }
    .search-box-wrapper input::placeholder { color:var(--muted); }
    .search-box-wrapper input:focus { border-color:var(--sky); background:var(--surface-2); }
    .voice-btn { position:absolute; right:8px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--sky); cursor:pointer; font-size:1.1rem; padding:4px; }
    .voice-btn:hover { color:#fff; }
    .suggestion-box { position:absolute; top:110%; left:0; width:100%; background:#0f172a; border:1px solid var(--border); border-radius:15px; display:none; overflow:hidden; z-index:1001; box-shadow:var(--shadow-md); }
    .suggestion-item { display:flex; align-items:center; padding:10px 14px; border-bottom:1px solid var(--border); transition:.2s; }
    .suggestion-item:last-child { border-bottom:none; }
    .suggestion-item:hover { background:var(--surface-2); }
    .suggestion-img { width:38px; height:38px; border-radius:8px; margin-right:12px; object-fit:cover; }

    /* Nav links (desktop) */
    .nav-links { display:flex; align-items:center; gap:28px; }
    .nav-links a { font-size:.9rem; font-weight:600; color:var(--muted); transition:color .25s; position:relative; }
    .nav-links a::after { content:''; position:absolute; left:0; bottom:-6px; width:0; height:2px; background:var(--grad-main); transition:width .3s; border-radius:2px; }
    .nav-links a:hover { color:var(--text); }
    .nav-links a:hover::after { width:100%; }

    /* Auth area (PHP) */
    .nav-cta { display:flex; align-items:center; gap:14px; flex-shrink:0; }
    .user-info { display:flex; align-items:center; gap:12px; }
    .user-info span { font-size:14px; color:var(--muted); }
    .logout-btn { color:#ef4444; font-size:18px; background:none; border:none; cursor:pointer; padding:4px; transition:.2s; }
    .logout-btn:hover { color:#ff6b6b; transform:scale(1.1); }

    /* Hamburger */
    .hamburger { display:none; flex-direction:column; gap:5px; background:transparent; padding:8px; }
    .hamburger span { width:26px; height:2.5px; background:var(--text); border-radius:2px; transition:.3s; }
    .mobile-menu { position:fixed; top:var(--nav-h); left:0; right:0; z-index:999; background:rgba(6,8,22,.96); backdrop-filter:blur(18px); border-bottom:1px solid var(--border); display:flex; flex-direction:column; padding:18px 22px; gap:6px; transform:translateY(-130%); transition:transform .4s cubic-bezier(.4,0,.2,1); visibility:hidden; }
    .mobile-menu.open { transform:translateY(0); visibility:visible; }
    .mobile-menu a { padding:12px 8px; font-weight:600; color:var(--muted); border-radius:10px; }
    .mobile-menu a:hover { background:var(--surface); color:var(--text); }

    /* =========================================================
       HERO
    ========================================================= */
    .hero { padding-top:calc(var(--nav-h) + 70px); padding-bottom:90px; overflow:hidden; }
    .hero-grid { display:grid; grid-template-columns:1.05fr .95fr; gap:50px; align-items:center; }
    .hero h1 { font-size:clamp(2.4rem,5.4vw,4.1rem); font-weight:900; letter-spacing:-1.5px; margin-bottom:22px; text-wrap:balance; }
    .hero p.lead { color:var(--muted); font-size:1.12rem; max-width:540px; margin-bottom:32px; }
    .hero-actions { display:flex; gap:16px; flex-wrap:wrap; margin-bottom:34px; }
    .hero-trust { display:flex; align-items:center; gap:22px; flex-wrap:wrap; color:var(--muted); font-size:.9rem; }
    .hero-trust .stars { color:#fbbf24; letter-spacing:2px; }
    .hero-visual { position:relative; }
    .hero-card-img { border-radius:var(--radius-lg); overflow:hidden; border:1px solid var(--border); box-shadow:var(--shadow-md); position:relative; }
    .hero-card-img img { width:100%; height:440px; object-fit:cover; }
    .hero-card-img::after { content:''; position:absolute; inset:0; background:linear-gradient(180deg,transparent 40%,rgba(6,8,22,.6)); }
    .float-chip { position:absolute; padding:14px 18px; border-radius:16px; font-weight:700; font-size:.9rem; display:flex; align-items:center; gap:10px; animation:floaty 5s ease-in-out infinite; }
    .float-chip .ic { width:34px; height:34px; border-radius:10px; display:grid; place-items:center; background:var(--grad-soft); color:#fff; }
    .float-chip.c1 { top:-22px; left:-22px; }
    .float-chip.c2 { bottom:50px; right:-28px; animation-delay:-1.5s; }
    .float-chip.c3 { bottom:-22px; left:30px; animation-delay:-3s; }
    @keyframes floaty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-16px)} }
    .float-chip small { display:block; color:var(--muted); font-weight:500; font-size:.72rem; }

    /* MARQUEE */
    .marquee-wrap { padding:36px 0; border-top:1px solid var(--border); border-bottom:1px solid var(--border); background:rgba(255,255,255,.015); }
    .marquee { display:flex; overflow:hidden; -webkit-mask-image:linear-gradient(90deg,transparent,#000 12%,#000 88%,transparent); mask-image:linear-gradient(90deg,transparent,#000 12%,#000 88%,transparent); }
    .marquee-track { display:flex; gap:60px; padding-right:60px; animation:scrollX 28s linear infinite; flex-shrink:0; }
    .marquee:hover .marquee-track { animation-play-state:paused; }
    @keyframes scrollX { to{transform:translateX(-100%)} }
    .brand-logo { font-family:'Outfit'; font-weight:800; font-size:1.5rem; color:var(--muted); opacity:.7; transition:.3s; white-space:nowrap; }
    .brand-logo:hover { color:var(--sky); opacity:1; }

    /* =========================================================
       TRA CỨU BẢO HÀNH (từ file PHP cũ)
    ========================================================= */
    .warranty-card { padding:50px 40px; border-radius:30px; text-align:center; }
    .warranty-form { display:flex; gap:12px; max-width:560px; margin:28px auto 0; flex-wrap:wrap; }
    .warranty-form input { flex:1; min-width:200px; padding:14px 24px; border-radius:99px; background:rgba(255,255,255,0.05); border:1px solid var(--border); color:#fff; font-family:'Plus Jakarta Sans',sans-serif; outline:none; font-size:.95rem; transition:.3s; }
    .warranty-form input:focus { border-color:var(--sky); background:var(--surface-2); }
    .warranty-form input::placeholder { color:var(--muted); }

    /* SERVICES */
    .services-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:22px; margin-top:56px; }
    .service-card { padding:28px 24px; border-radius:var(--radius); position:relative; overflow:hidden; transition:transform .35s,box-shadow .35s,border-color .35s; }
    .service-card::before { content:''; position:absolute; inset:0; opacity:0; transition:opacity .35s; background:radial-gradient(circle at 50% 0%,rgba(124,58,237,.18),transparent 70%); }
    .service-card:hover { transform:translateY(-8px); border-color:var(--sky); box-shadow:var(--shadow-md); }
    .service-card:hover::before { opacity:1; }
    .service-ic { width:56px; height:56px; border-radius:15px; display:grid; place-items:center; font-size:1.5rem; background:var(--grad-soft); color:#fff; margin-bottom:18px; box-shadow:var(--shadow-glow); transition:transform .35s; }
    .service-card:hover .service-ic { transform:rotate(-8deg) scale(1.08); }
    .service-card h3 { font-size:1.12rem; font-weight:700; margin-bottom:8px; }
    .service-card p { color:var(--muted); font-size:.92rem; }

    /* STATS */
    .stats-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:18px; }
    .stat-card { padding:30px 18px; border-radius:var(--radius); text-align:center; transition:transform .3s; }
    .stat-card:hover { transform:translateY(-6px); }
    .stat-num { font-family:'Outfit'; font-size:clamp(1.7rem,3vw,2.4rem); font-weight:800; background:var(--grad-text); -webkit-background-clip:text; background-clip:text; color:transparent; }
    .stat-label { color:var(--muted); font-size:.88rem; margin-top:6px; }

    /* ACHIEVEMENTS */
    .achieve-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:20px; margin-top:54px; }
    .achieve-card { padding:30px 22px; border-radius:var(--radius); text-align:center; transition:.35s; }
    .achieve-card:hover { transform:translateY(-8px); border-color:var(--violet); box-shadow:var(--shadow-md); }
    .achieve-ic { font-size:2.2rem; margin-bottom:14px; }
    .achieve-card h4 { font-size:1.02rem; font-weight:700; }
    .achieve-card p { color:var(--muted); font-size:.85rem; margin-top:6px; }

    /* PROJECTS */
    .projects-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; margin-top:56px; }
    .project-card { border-radius:var(--radius); overflow:hidden; position:relative; border:1px solid var(--border); box-shadow:var(--shadow-sm); transition:transform .4s,box-shadow .4s; }
    .project-card img { width:100%; height:260px; object-fit:cover; transition:transform .6s; }
    .project-card:hover { transform:translateY(-8px); box-shadow:var(--shadow-md); }
    .project-card:hover img { transform:scale(1.1); }
    .project-overlay { position:absolute; inset:0; display:flex; flex-direction:column; justify-content:flex-end; padding:24px; background:linear-gradient(180deg,transparent 35%,rgba(6,8,22,.92)); }
    .project-overlay .tag { align-self:flex-start; font-size:.72rem; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--sky); background:rgba(56,189,248,.12); border:1px solid rgba(56,189,248,.3); padding:5px 12px; border-radius:999px; margin-bottom:12px; }
    .project-overlay h3 { font-size:1.25rem; font-weight:700; }
    .project-overlay p { color:var(--muted); font-size:.9rem; margin-top:4px; }

    /* TIMELINE */
    .timeline { position:relative; margin-top:60px; }
    .timeline::before { content:''; position:absolute; left:50%; top:0; bottom:0; width:3px; transform:translateX(-50%); background:var(--grad-main); border-radius:3px; opacity:.55; }
    .tl-item { display:grid; grid-template-columns:1fr 80px 1fr; align-items:center; margin-bottom:12px; }
    .tl-content { padding:24px; border-radius:var(--radius); transition:.35s; }
    .tl-content:hover { transform:translateY(-5px); border-color:var(--cyan); box-shadow:var(--shadow-md); }
    .tl-content h4 { font-size:1.15rem; font-weight:700; margin-bottom:6px; }
    .tl-content p { color:var(--muted); font-size:.92rem; }
    .tl-dot { width:56px; height:56px; border-radius:50%; background:var(--grad-main); display:grid; place-items:center; font-family:'Outfit'; font-weight:800; font-size:1.3rem; color:#fff; margin:0 auto; box-shadow:var(--shadow-glow); z-index:1; }
    .tl-item:nth-child(odd) .tl-content { grid-column:1; text-align:right; }
    .tl-item:nth-child(odd) .tl-spacer { grid-column:3; }
    .tl-item:nth-child(even) .tl-spacer { grid-column:1; }
    .tl-item:nth-child(even) .tl-content { grid-column:3; text-align:left; }
    .tl-dot { grid-column:2; }

    /* TESTIMONIALS */
    .testi-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; margin-top:56px; }
    .testi-card { padding:30px 26px; border-radius:var(--radius); transition:.35s; }
    .testi-card:hover { transform:translateY(-8px); border-color:var(--indigo); box-shadow:var(--shadow-md); }
    .testi-stars { color:#fbbf24; letter-spacing:2px; margin-bottom:14px; }
    .testi-text { color:var(--text); font-size:.98rem; opacity:.92; margin-bottom:22px; font-style:italic; }
    .testi-author { display:flex; align-items:center; gap:14px; }
    .testi-author img { width:52px; height:52px; border-radius:50%; object-fit:cover; border:2px solid var(--border); }
    .testi-author strong { display:block; font-size:.98rem; }
    .testi-author span { color:var(--muted); font-size:.82rem; }

    /* PRICING */
    .pricing-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:26px; margin-top:56px; align-items:stretch; }
    .price-card { padding:38px 30px; border-radius:var(--radius-lg); display:flex; flex-direction:column; transition:.35s; position:relative; }
    .price-card:hover { transform:translateY(-10px); box-shadow:var(--shadow-md); }
    .price-card.featured { background:var(--grad-main); border:none; box-shadow:var(--shadow-glow); transform:scale(1.04); }
    .price-card.featured:hover { transform:scale(1.04) translateY(-10px); }
    .price-badge { position:absolute; top:-14px; left:50%; transform:translateX(-50%); background:#fbbf24; color:#1a1a2e; font-weight:800; font-size:.74rem; letter-spacing:1px; text-transform:uppercase; padding:6px 16px; border-radius:999px; }
    .price-card h3 { font-size:1.35rem; font-weight:700; margin-bottom:6px; }
    .price-card .desc { color:var(--muted); font-size:.9rem; margin-bottom:22px; }
    .price-card.featured .desc { color:rgba(255,255,255,.85); }
    .price-amount { font-family:'Outfit'; font-size:2.6rem; font-weight:800; margin-bottom:4px; }
    .price-amount small { font-size:1rem; font-weight:500; color:var(--muted); }
    .price-card.featured .price-amount small { color:rgba(255,255,255,.85); }
    .price-features { margin:26px 0; display:flex; flex-direction:column; gap:12px; flex:1; }
    .price-features li { display:flex; align-items:center; gap:10px; font-size:.94rem; color:var(--muted); }
    .price-card.featured .price-features li { color:rgba(255,255,255,.92); }
    .price-features li .chk { color:var(--cyan); font-weight:800; }
    .price-card.featured .price-features li .chk { color:#fff; }

    /* BLOG */
    .blog-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:26px; margin-top:56px; }
    .blog-card { border-radius:var(--radius); overflow:hidden; transition:.35s; display:flex; flex-direction:column; }
    .blog-card:hover { transform:translateY(-8px); border-color:var(--sky); box-shadow:var(--shadow-md); }
    .blog-thumb { height:200px; overflow:hidden; }
    .blog-thumb img { width:100%; height:100%; object-fit:cover; transition:transform .6s; }
    .blog-card:hover .blog-thumb img { transform:scale(1.08); }
    .blog-body { padding:24px; }
    .blog-cat { font-size:.76rem; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--sky); }
    .blog-body h3 { font-size:1.12rem; font-weight:700; margin:10px 0 8px; }
    .blog-body p { color:var(--muted); font-size:.9rem; }
    .blog-meta { margin-top:16px; display:flex; gap:16px; color:var(--muted); font-size:.8rem; }

    /* FAQ */
    .faq-list { max-width:820px; margin:50px auto 0; display:flex; flex-direction:column; gap:14px; }
    .faq-item { border-radius:var(--radius); overflow:hidden; transition:border-color .3s; }
    .faq-item.active { border-color:var(--sky); }
    .faq-q { width:100%; text-align:left; background:transparent; color:var(--text); padding:20px 24px; font-size:1.02rem; font-weight:600; display:flex; justify-content:space-between; align-items:center; gap:16px; }
    .faq-q .plus { font-size:1.4rem; color:var(--sky); transition:transform .3s; flex-shrink:0; }
    .faq-item.active .plus { transform:rotate(45deg); }
    .faq-a { max-height:0; overflow:hidden; transition:max-height .4s ease; color:var(--muted); padding:0 24px; }
    .faq-a p { padding-bottom:22px; font-size:.95rem; }

    /* CONTACT FORM */
    .contact-grid { display:grid; grid-template-columns:1fr 1.1fr; gap:44px; align-items:center; margin-top:50px; }
    .contact-info h3 { font-size:1.8rem; font-weight:800; margin-bottom:16px; }
    .contact-info p { color:var(--muted); margin-bottom:26px; }
    .contact-line { display:flex; align-items:center; gap:14px; margin-bottom:18px; }
    .contact-line .ic { width:46px; height:46px; border-radius:13px; display:grid; place-items:center; background:var(--grad-soft); color:#fff; font-size:1.1rem; flex-shrink:0; }
    .contact-line strong { display:block; font-size:.8rem; color:var(--muted); font-weight:500; }
    .contact-line span { font-weight:700; }
    .contact-form { padding:36px; border-radius:var(--radius-lg); }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .field { margin-bottom:18px; }
    .field label { display:block; font-size:.85rem; font-weight:600; margin-bottom:8px; color:var(--muted); }
    .field input,.field select,.field textarea { width:100%; padding:13px 16px; border-radius:12px; background:rgba(255,255,255,.04); border:1px solid var(--border); color:var(--text); font-family:inherit; font-size:.95rem; transition:border-color .25s,box-shadow .25s; }
    .field textarea { resize:vertical; min-height:120px; }
    .field input:focus,.field select:focus,.field textarea:focus { outline:none; border-color:var(--sky); box-shadow:0 0 0 3px rgba(56,189,248,.15); }
    .field select option { background:#0a0e27; }
    .field .err { color:#fb7185; font-size:.8rem; margin-top:6px; display:none; }
    .field.invalid input,.field.invalid select,.field.invalid textarea { border-color:#fb7185; }
    .field.invalid .err { display:block; }
    .form-msg { padding:14px 18px; border-radius:12px; background:rgba(34,197,94,.12); border:1px solid rgba(34,197,94,.4); color:#86efac; font-weight:600; font-size:.92rem; margin-bottom:18px; display:none; }
    .form-msg.show { display:block; animation:fadeUp .5s; }
    @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }

    /* FOOTER */
    footer { padding:70px 0 30px; border-top:1px solid var(--border); background:rgba(255,255,255,.015); margin-top:90px; }
    .footer-grid { display:grid; grid-template-columns:1.6fr 1fr 1fr 1.2fr; gap:40px; margin-bottom:50px; }
    .footer-col h4 { font-size:1rem; font-weight:700; margin-bottom:18px; }
    .footer-col p,.footer-col li { color:var(--muted); font-size:.92rem; margin-bottom:12px; }
    .footer-col a { transition:color .25s; }
    .footer-col a:hover { color:var(--sky); }
    .footer-brand-desc { margin:16px 0 22px; max-width:320px; }
    .socials { display:flex; gap:12px; }
    .socials a { width:42px; height:42px; border-radius:12px; display:grid; place-items:center; background:var(--surface); border:1px solid var(--border); transition:.3s; }
    .socials a:hover { background:var(--grad-main); transform:translateY(-4px); border-color:transparent; }
    .footer-bottom { padding-top:26px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; color:var(--muted); font-size:.88rem; }

    /* BACK TO TOP */
    #backTop { position:fixed; bottom:28px; right:28px; z-index:900; width:52px; height:52px; border-radius:50%; background:var(--grad-main); color:#fff; display:grid; place-items:center; font-size:1.3rem; box-shadow:var(--shadow-glow); opacity:0; visibility:hidden; transform:translateY(20px); transition:.35s; }
    #backTop.show { opacity:1; visibility:visible; transform:translateY(0); }
    #backTop:hover { transform:translateY(-4px) scale(1.08); }

    /* VOICE OVERLAY */
    #voiceOverlay { position:fixed; inset:0; background:rgba(6,8,22,0.95); z-index:10000; display:none; flex-direction:column; align-items:center; justify-content:center; backdrop-filter:blur(10px); }
    .voice-wave { width:80px; height:80px; border-radius:50%; background:var(--sky); animation:pulse-blue 1.5s infinite; display:grid; place-items:center; font-size:2rem; }
    @keyframes pulse-blue { 0%{transform:scale(.95);box-shadow:0 0 0 0 rgba(56,189,248,.7)} 70%{transform:scale(1.1);box-shadow:0 0 0 30px rgba(56,189,248,0)} 100%{transform:scale(.95);box-shadow:0 0 0 0 rgba(56,189,248,0)} }

    /* SCROLL REVEAL */
    .reveal { opacity:0; transform:translateY(40px); transition:opacity .8s ease,transform .8s cubic-bezier(.2,.7,.2,1); }
    .reveal.in { opacity:1; transform:translateY(0); }
    .reveal.left { transform:translateX(-50px); }
    .reveal.left.in { transform:translateX(0); }
    .reveal.right { transform:translateX(50px); }
    .reveal.right.in { transform:translateX(0); }
    .reveal.zoom { transform:scale(.9); }
    .reveal.zoom.in { transform:scale(1); }
    .d1{transition-delay:.08s} .d2{transition-delay:.16s} .d3{transition-delay:.24s}
    .d4{transition-delay:.32s} .d5{transition-delay:.4s} .d6{transition-delay:.48s}

    /* RESPONSIVE */
    @media(max-width:1024px){
      .services-grid{grid-template-columns:repeat(3,1fr)}
      .stats-grid{grid-template-columns:repeat(3,1fr)}
      .achieve-grid{grid-template-columns:repeat(3,1fr)}
      .projects-grid,.testi-grid,.blog-grid{grid-template-columns:repeat(2,1fr)}
      .footer-grid{grid-template-columns:1fr 1fr}
    }
    @media(max-width:880px){
      .nav-links,.nav-cta .btn-primary{display:none}
      .hamburger{display:flex}
      .nav-search{display:none}
      .hero-grid{grid-template-columns:1fr;text-align:center}
      .hero p.lead{margin-left:auto;margin-right:auto}
      .hero-actions,.hero-trust{justify-content:center}
      .hero-visual{margin-top:30px}
      .contact-grid{grid-template-columns:1fr}
      .pricing-grid{grid-template-columns:1fr;max-width:460px;margin-inline:auto}
      .price-card.featured{transform:scale(1)}
      .price-card.featured:hover{transform:translateY(-10px)}
      .timeline::before{left:26px}
      .tl-item{grid-template-columns:56px 1fr;gap:20px}
      .tl-dot{grid-column:1!important}
      .tl-content,.tl-item:nth-child(odd) .tl-content,.tl-item:nth-child(even) .tl-content{grid-column:2!important;text-align:left!important}
      .tl-spacer{display:none}
    }
    @media(max-width:640px){
      .section-pad{padding:72px 0}
      .services-grid,.stats-grid,.achieve-grid,.projects-grid,.testi-grid,.blog-grid{grid-template-columns:1fr}
      .form-row{grid-template-columns:1fr}
      .footer-grid{grid-template-columns:1fr}
      .float-chip.c1{left:0} .float-chip.c2{right:0}
      .warranty-form{flex-direction:column}
    }
  </style>
</head>
<body>

  <!-- LOADING SCREEN -->
  <div id="loader">
    <div class="loader-ring"></div>
    <div class="loader-brand">Tech<span>Fix</span></div>
  </div>

  <!-- NỀN ĐỘNG -->
  <div class="bg-orbs" aria-hidden="true">
    <div class="orb b1"></div>
    <div class="orb b2"></div>
    <div class="orb b3"></div>
  </div>
  <div class="bg-grid" aria-hidden="true"></div>
  <div class="particles" id="particles" aria-hidden="true"></div>

  <!-- ============================================================
       NAVBAR — có Search + Auth (PHP)
  ============================================================ -->
  <header class="nav" id="navbar">
    <div class="container nav-inner">

      <a href="#hero" class="logo" aria-label="TechFix trang chủ">
        <span class="logo-mark">⚡</span>
        Tech<span>Fix</span>
      </a>

      <!-- Thanh tìm kiếm giọng nói (ẩn trên mobile, hiện trên desktop) -->
      <div class="nav-search">
        <form action="/TechFixPHP/Customer/Service.php" method="GET" class="search-box-wrapper">
          <input
            type="text"
            name="search"
            id="voiceSearchInput"
            placeholder="Tìm dịch vụ (nói 'Sửa máy tính')..."
            autocomplete="off"
          >
          <button type="button" class="voice-btn" onclick="startVoiceSearch()" title="Tìm bằng giọng nói">
            <i class="fa-solid fa-microphone"></i>
          </button>
          <div id="search-results" class="suggestion-box"></div>
        </form>
      </div>

      <!-- Nav links (desktop) -->
      <nav class="nav-links" aria-label="Điều hướng chính">
        <a href="#services">Dịch vụ</a>
        <a href="#stats">Thống kê</a>
        <a href="#projects">Dự án</a>
        <a href="#pricing">Bảng giá</a>
        <a href="#blog">Tin tức</a>
        <a href="#faq">FAQ</a>
        <?php if ($role === 'admin'): ?>
          <a href="/TechFixPHP/pages/admin/dashboard.php" style="color:var(--sky)">
            <i class="fa-solid fa-gauge-high"></i> Quản trị
          </a>
        <?php elseif ($role === 'technical'): ?>
          <a href="/TechFixPHP/pages/admin/tech_schedule.php">
            <i class="fa-solid fa-calendar-check"></i> Lịch làm
          </a>
        <?php endif; ?>
        <a href="/TechFixPHP/pages/public_page/forum.php">
          <i class="fa-solid fa-comments"></i> Hỏi đáp
        </a>
        <a href="/TechFixPHP/Customer/my_booking.php">
          <i class="fa-solid fa-calendar"></i> Đặt lịch
        </a>
      </nav>

      <!-- Auth CTA -->
      <div class="nav-cta">
        <?php if (!$isLoggedIn): ?>
          <a href="/TechFixPHP/pages/public_page/login.php" class="btn btn-primary">
            <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
          </a>
        <?php else: ?>
          <div class="user-info">
            <button
              id="install-app-btn"
              class="btn btn-ghost"
              style="display:none; padding:8px 14px; font-size:12px;"
            >📲 Tải App</button>
            <span>Xin chào, <b><?= htmlspecialchars($name) ?></b></span>
            <a href="/TechFixPHP/pages/public_page/login.php" class="logout-btn" title="Đăng xuất">
              <i class="fa-solid fa-power-off"></i>
            </a>
          </div>
        <?php endif; ?>
        <button class="hamburger" id="hamburger" aria-label="Mở menu" aria-expanded="false">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </header>

  <!-- Menu mobile -->
  <nav class="mobile-menu" id="mobileMenu" aria-label="Menu di động">
    <a href="#services">Dịch vụ</a>
    <a href="#stats">Thống kê</a>
    <a href="#projects">Dự án</a>
    <a href="#process">Quy trình</a>
    <a href="#pricing">Bảng giá</a>
    <a href="#blog">Tin tức</a>
    <a href="#faq">FAQ</a>
    <a href="/TechFixPHP/Customer/my_booking.php">Đặt lịch</a>
    <a href="/TechFixPHP/pages/public_page/forum.php">Hỏi đáp</a>
    <?php if (!$isLoggedIn): ?>
      <a href="/TechFixPHP/pages/public_page/login.php" class="btn btn-primary" style="margin-top:10px; text-align:center;">Đăng nhập</a>
    <?php else: ?>
      <a href="/TechFixPHP/pages/public_page/login.php" style="color:#ef4444;">
        <i class="fa-solid fa-power-off"></i> Đăng xuất
      </a>
    <?php endif; ?>
  </nav>

  <main>

    <!-- ============ HERO ============ -->
    <section class="hero" id="hero">
      <div class="container hero-grid">
        <div class="hero-text">
          <span class="eyebrow reveal">★ Đối tác công nghệ tin cậy toàn quốc</span>
          <h1 class="reveal d1">Giải pháp sửa chữa <span class="gradient-text">công nghệ toàn diện</span></h1>
          <p class="lead reveal d2">TechFix cung cấp dịch vụ sửa chữa và hỗ trợ kỹ thuật cho cá nhân và doanh nghiệp trên toàn quốc — nhanh chóng, chuyên nghiệp và bảo hành dài hạn.</p>
          <div class="hero-actions reveal d3">
            <a href="/TechFixPHP/Customer/book.php" class="btn btn-primary">
              Đặt lịch ngay <i class="fa-solid fa-arrow-right"></i>
            </a>
            <a href="#services" class="btn btn-ghost">Xem dịch vụ</a>
          </div>
          <div class="hero-trust reveal d4">
            <div><span class="stars">★★★★★</span> 4.9/5 đánh giá</div>
            <div>50.000+ khách hàng tin dùng</div>
            <div>Hỗ trợ 24/7</div>
          </div>
        </div>
        <div class="hero-visual reveal right d2">
          <div class="hero-card-img">
            <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=900&q=80"
                 alt="Kỹ thuật viên TechFix đang sửa chữa thiết bị" loading="eager" />
          </div>
          <div class="float-chip glass c1">
            <span class="ic">⚙️</span>
            <div>Bảo hành 12 tháng <small>Cam kết chất lượng</small></div>
          </div>
          <div class="float-chip glass c2">
            <span class="ic">⚡</span>
            <div>Phản hồi 15 phút <small>Hỗ trợ tức thì</small></div>
          </div>
          <div class="float-chip glass c3">
            <span class="ic">🛡️</span>
            <div>An toàn dữ liệu <small>Bảo mật tuyệt đối</small></div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============ MARQUEE ============ -->
    <section class="marquee-wrap" aria-label="Khách hàng tiêu biểu">
      <div class="container">
        <p class="center" style="color:var(--muted); font-size:.85rem; letter-spacing:1px; text-transform:uppercase; margin-bottom:24px;">Được tin dùng bởi các thương hiệu hàng đầu</p>
      </div>
      <div class="marquee">
        <div class="marquee-track" id="brandTrack"></div>
        <div class="marquee-track" aria-hidden="true" id="brandTrack2"></div>
      </div>
    </section>

    <!-- ============ TRA CỨU BẢO HÀNH (PHP) ============ -->
    <section id="warranty" class="section-pad" style="padding-bottom:60px;">
      <div class="container">
        <div class="warranty-card glass reveal">
          <i class="fa-solid fa-shield-halved" style="font-size:52px; color:var(--sky); margin-bottom:20px;"></i>
          <h2 class="section-title">Tra cứu <span class="gradient-text">bảo hành điện tử</span></h2>
          <p class="section-sub">Kiểm tra thời hạn bảo hành nhanh chóng bằng mã đơn hàng hoặc số điện thoại.</p>
          <form action="/TechFixPHP/warranty.php" method="GET" class="warranty-form">
            <input type="text" name="keyword" placeholder="Nhập mã đơn hoặc SĐT của bạn..." required>
            <button type="submit" class="btn btn-primary">
              <i class="fa-solid fa-magnifying-glass"></i> Tra cứu ngay
            </button>
          </form>
        </div>
      </div>
    </section>

    <!-- ============ DỊCH VỤ ============ -->
    <section class="section-pad" id="services">
      <div class="container">
        <div class="center">
          <span class="eyebrow reveal">Dịch vụ của chúng tôi</span>
          <h2 class="section-title reveal d1">Đa dịch vụ <span class="gradient-text">sửa chữa & kỹ thuật</span></h2>
          <p class="section-sub reveal d2">Một điểm đến cho mọi nhu cầu công nghệ của bạn — từ thiết bị cá nhân đến hạ tầng doanh nghiệp.</p>
        </div>
        <div class="services-grid" id="servicesGrid"></div>
      </div>
    </section>

    <!-- ============ THỐNG KÊ ============ -->
    <section class="section-pad" id="stats" style="padding-top:30px;">
      <div class="container">
        <div class="center" style="margin-bottom:50px;">
          <span class="eyebrow reveal">Con số ấn tượng</span>
          <h2 class="section-title reveal d1">Thành quả <span class="gradient-text">đáng tự hào</span></h2>
        </div>
        <div class="stats-grid" id="statsGrid"></div>
      </div>
    </section>

    <!-- ============ THÀNH TỰU ============ -->
    <section class="section-pad" id="achievements" style="padding-top:30px;">
      <div class="container">
        <div class="center">
          <span class="eyebrow reveal">Thành tựu nổi bật</span>
          <h2 class="section-title reveal d1">Khẳng định <span class="gradient-text">vị thế dẫn đầu</span></h2>
        </div>
        <div class="achieve-grid" id="achieveGrid"></div>
      </div>
    </section>

    <!-- ============ DỰ ÁN TIÊU BIỂU ============ -->
    <section class="section-pad" id="projects">
      <div class="container">
        <div class="center">
          <span class="eyebrow reveal">Dự án tiêu biểu</span>
          <h2 class="section-title reveal d1">Những công trình <span class="gradient-text">chúng tôi tự hào</span></h2>
          <p class="section-sub reveal d2">Hàng trăm dự án quy mô lớn được triển khai thành công trên toàn quốc.</p>
        </div>
        <div class="projects-grid" id="projectsGrid"></div>
      </div>
    </section>

    <!-- ============ QUY TRÌNH ============ -->
    <section class="section-pad" id="process" style="padding-top:30px;">
      <div class="container">
        <div class="center">
          <span class="eyebrow reveal">Quy trình làm việc</span>
          <h2 class="section-title reveal d1">6 bước <span class="gradient-text">chuyên nghiệp</span></h2>
          <p class="section-sub reveal d2">Quy trình minh bạch, rõ ràng giúp bạn yên tâm từ đầu đến cuối.</p>
        </div>
        <div class="timeline" id="timeline"></div>
      </div>
    </section>

    <!-- ============ TESTIMONIALS ============ -->
    <section class="section-pad" id="testimonials">
      <div class="container">
        <div class="center">
          <span class="eyebrow reveal">Khách hàng nói gì</span>
          <h2 class="section-title reveal d1">Đánh giá từ <span class="gradient-text">khách hàng thực tế</span></h2>
        </div>
        <div class="testi-grid" id="testiGrid"></div>
      </div>
    </section>

    <!-- ============ BẢNG GIÁ ============ -->
    <section class="section-pad" id="pricing" style="padding-top:30px;">
      <div class="container">
        <div class="center">
          <span class="eyebrow reveal">Bảng giá dịch vụ</span>
          <h2 class="section-title reveal d1">Gói dịch vụ <span class="gradient-text">linh hoạt</span></h2>
          <p class="section-sub reveal d2">Lựa chọn gói phù hợp với nhu cầu của cá nhân và doanh nghiệp.</p>
        </div>
        <div class="pricing-grid" id="pricingGrid"></div>
      </div>
    </section>

    <!-- ============ BLOG ============ -->
    <section class="section-pad" id="blog">
      <div class="container">
        <div class="center">
          <span class="eyebrow reveal">Blog & Tin tức</span>
          <h2 class="section-title reveal d1">Kiến thức <span class="gradient-text">công nghệ mới nhất</span></h2>
        </div>
        <div class="blog-grid" id="blogGrid"></div>
      </div>
    </section>

    <!-- ============ FAQ ============ -->
    <section class="section-pad" id="faq" style="padding-top:30px;">
      <div class="container">
        <div class="center">
          <span class="eyebrow reveal">Câu hỏi thường gặp</span>
          <h2 class="section-title reveal d1">Giải đáp <span class="gradient-text">thắc mắc của bạn</span></h2>
        </div>
        <div class="faq-list" id="faqList"></div>
      </div>
    </section>

    <!-- ============ LIÊN HỆ ============ -->
    <section class="section-pad" id="contact">
      <div class="container">
        <div class="center">
          <span class="eyebrow reveal">Liên hệ</span>
          <h2 class="section-title reveal d1">Sẵn sàng <span class="gradient-text">hỗ trợ bạn ngay</span></h2>
        </div>
        <div class="contact-grid">
          <div class="contact-info reveal left">
            <h3>Kết nối với TechFix</h3>
            <p>Để lại thông tin, đội ngũ kỹ thuật của chúng tôi sẽ liên hệ tư vấn miễn phí trong vòng 15 phút.</p>
            <div class="contact-line"><span class="ic">📞</span><div><strong>Hotline 24/7</strong><span>1900 6868</span></div></div>
            <div class="contact-line"><span class="ic">✉️</span><div><strong>Email</strong><span>support@techfix.vn</span></div></div>
            <div class="contact-line"><span class="ic">📍</span><div><strong>Địa chỉ</strong><span>Tầng 12, Tòa nhà TechFix, Cầu Giấy, Hà Nội</span></div></div>
            <div class="contact-line"><span class="ic">🕐</span><div><strong>Giờ làm việc</strong><span>Hỗ trợ trực tuyến 24/7</span></div></div>
          </div>
          <form class="contact-form glass reveal right" id="contactForm" novalidate>
            <div class="form-msg" id="formMsg">✓ Cảm ơn bạn! Yêu cầu đã được gửi thành công. Chúng tôi sẽ liên hệ sớm nhất.</div>
            <div class="form-row">
              <div class="field">
                <label for="cf-name">Họ và tên *</label>
                <input type="text" id="cf-name" name="name" placeholder="Nguyễn Văn A" />
                <div class="err">Vui lòng nhập họ tên (ít nhất 2 ký tự).</div>
              </div>
              <div class="field">
                <label for="cf-email">Email *</label>
                <input type="email" id="cf-email" name="email" placeholder="email@example.com" />
                <div class="err">Email không hợp lệ.</div>
              </div>
            </div>
            <div class="form-row">
              <div class="field">
                <label for="cf-phone">Số điện thoại *</label>
                <input type="tel" id="cf-phone" name="phone" placeholder="09xx xxx xxx" />
                <div class="err">Số điện thoại không hợp lệ (10-11 số).</div>
              </div>
              <div class="field">
                <label for="cf-service">Dịch vụ cần hỗ trợ *</label>
                <select id="cf-service" name="service">
                  <option value="">-- Chọn dịch vụ --</option>
                  <option>Sửa chữa máy tính / laptop</option>
                  <option>Sửa chữa điện thoại</option>
                  <option>Cứu dữ liệu</option>
                  <option>Camera giám sát</option>
                  <option>Thiết kế mạng doanh nghiệp</option>
                  <option>Bảo trì hệ thống CNTT</option>
                  <option>Website & Hosting</option>
                  <option>Khác</option>
                </select>
                <div class="err">Vui lòng chọn dịch vụ.</div>
              </div>
            </div>
            <div class="field">
              <label for="cf-message">Nội dung yêu cầu *</label>
              <textarea id="cf-message" name="message" placeholder="Mô tả chi tiết vấn đề bạn đang gặp phải..."></textarea>
              <div class="err">Vui lòng nhập nội dung (ít nhất 10 ký tự).</div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">
              Gửi yêu cầu <i class="fa-solid fa-arrow-right"></i>
            </button>
          </form>
        </div>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <footer>
    <div class="container">
      <div class="footer-grid">
        <div class="footer-col">
          <a href="#hero" class="logo"><span class="logo-mark">⚡</span> Tech<span>Fix</span></a>
          <p class="footer-brand-desc">Giải pháp sửa chữa công nghệ toàn diện cho cá nhân và doanh nghiệp trên toàn quốc. Chuyên nghiệp — Nhanh chóng — Tin cậy.</p>
          <div class="socials">
            <a href="#" aria-label="Facebook">f</a>
            <a href="#" aria-label="YouTube">▶</a>
            <a href="#" aria-label="LinkedIn">in</a>
            <a href="#" aria-label="Zalo">Z</a>
          </div>
        </div>
        <div class="footer-col">
          <h4>Dịch vụ</h4>
          <ul>
            <li><a href="#services">Sửa máy tính</a></li>
            <li><a href="#services">Cứu dữ liệu</a></li>
            <li><a href="#services">Camera giám sát</a></li>
            <li><a href="#services">Mạng doanh nghiệp</a></li>
            <li><a href="#services">Website & Hosting</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Công ty</h4>
          <ul>
            <li><a href="#projects">Dự án</a></li>
            <li><a href="#achievements">Thành tựu</a></li>
            <li><a href="#blog">Tin tức</a></li>
            <li><a href="#testimonials">Đánh giá</a></li>
            <li><a href="#faq">FAQ</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Liên hệ</h4>
          <p>📞 Hotline: <strong>1900 6868</strong></p>
          <p>✉️ support@techfix.vn</p>
          <p>📍 Tầng 12, Tòa nhà TechFix, Cầu Giấy, Hà Nội</p>
          <p>🕐 Hỗ trợ 24/7</p>
        </div>
      </div>
      <div class="footer-bottom">
        <span>© 2026 TechFix. Bản quyền đã được bảo hộ.</span>
        <span>Thiết kế bởi đội ngũ TechFix · <a href="#">Chính sách bảo mật</a> · <a href="#">Điều khoản</a></span>
      </div>
    </div>
  </footer>

  <button id="backTop" aria-label="Lên đầu trang">↑</button>

  <!-- VOICE OVERLAY -->
  <div id="voiceOverlay">
    <div class="voice-wave"><i class="fa-solid fa-microphone"></i></div>
    <h2 id="voiceStatus" style="margin-top:30px; font-weight:300; font-family:'Outfit';">Đang nghe...</h2>
    <p style="color:var(--muted); margin-top:10px;">Hãy nói tên dịch vụ bạn cần tìm</p>
    <button
      onclick="closeVoiceSearch()"
      style="margin-top:30px; padding:10px 30px; background:#ff4757; color:#fff; border:none; border-radius:99px; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; font-weight:700;"
    >Hủy bỏ</button>
  </div>

  <!-- Chatbot (PHP include) -->
  <?php
    $chatbotPath = __DIR__ . '/pages/public_page/chatbot.php';
    if (file_exists($chatbotPath)) {
        include $chatbotPath;
    }
  ?>

  <script>
    /* =========================================================
       DATA
    ========================================================= */
    const brands = ['FPT','Viettel','VinGroup','Thế Giới Di Động','Samsung','LG','ASUS','Acer','Dell','HP'];

    const services = [
      {ic:'💻',t:'Sửa chữa máy tính',d:'Khắc phục mọi lỗi phần cứng, phần mềm cho PC.'},
      {ic:'💼',t:'Sửa chữa laptop',d:'Thay màn hình, bàn phím, pin, mainboard chính hãng.'},
      {ic:'📱',t:'Sửa chữa điện thoại',d:'Smartphone mọi hãng, linh kiện chuẩn, bảo hành.'},
      {ic:'🖨️',t:'Sửa chữa máy in',d:'Máy in laser, phun, đa năng cho văn phòng.'},
      {ic:'💾',t:'Cứu dữ liệu',d:'Phục hồi dữ liệu từ HDD, SSD, USB hỏng.'},
      {ic:'🔧',t:'Nâng cấp phần cứng',d:'RAM, SSD, CPU, card đồ họa tăng hiệu năng.'},
      {ic:'⚙️',t:'Cài đặt phần mềm',d:'Hệ điều hành, phần mềm bản quyền, driver.'},
      {ic:'🌐',t:'Thiết kế mạng doanh nghiệp',d:'Hệ thống mạng LAN/WAN ổn định, bảo mật.'},
      {ic:'🛠️',t:'Bảo trì hệ thống CNTT',d:'Bảo trì định kỳ, giám sát hạ tầng 24/7.'},
      {ic:'📹',t:'Camera giám sát',d:'Lắp đặt camera AI, giám sát từ xa qua điện thoại.'},
      {ic:'🚀',t:'Website & Hosting',d:'Thiết kế web chuẩn SEO, hosting tốc độ cao.'},
      {ic:'📡',t:'Hỗ trợ từ xa 24/7',d:'Kỹ thuật viên trực tuyến mọi lúc, mọi nơi.'},
    ];

    const stats = [
      {n:50000,suf:'+',l:'Khách hàng'},
      {n:120000,suf:'+',l:'Thiết bị đã sửa'},
      {n:15,suf:'+',l:'Năm kinh nghiệm'},
      {n:350,suf:'+',l:'Doanh nghiệp đối tác'},
      {n:5000000,suf:'+',l:'Lượt truy cập'},
      {n:120,suf:' Tỷ',l:'Doanh thu (VNĐ)'},
    ];

    const achievements = [
      {ic:'🏆',t:'Top 10 đơn vị CNTT',d:'Hỗ trợ kỹ thuật uy tín'},
      {ic:'😊',t:'98.7% hài lòng',d:'Mức độ hài lòng khách hàng'},
      {ic:'📜',t:'Chứng nhận ISO',d:'Tiêu chuẩn quốc tế'},
      {ic:'🤝',t:'Đối tác hàng đầu',d:'Hợp tác công nghệ lớn'},
      {ic:'✅',t:'120.000+ dự án',d:'Đã hoàn thành thành công'},
    ];

    const projects = [
      {img:'https://images.unsplash.com/photo-1538108149393-fbbd81895907?w=700&q=80',tag:'Y tế',t:'Hệ thống CNTT bệnh viện',d:'Triển khai hạ tầng cho 1.200 giường bệnh.'},
      {img:'https://images.unsplash.com/photo-1565514020179-026b92b84bb6?w=700&q=80',tag:'Công nghiệp',t:'Hệ thống camera nhà máy',d:'320 camera AI giám sát toàn nhà máy.'},
      {img:'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=700&q=80',tag:'Doanh nghiệp',t:'Nâng cấp hạ tầng mạng',d:'Mạng tốc độ cao cho 2.000 nhân sự.'},
      {img:'https://images.unsplash.com/photo-1591405351990-4726e331f141?w=700&q=80',tag:'Hạ tầng',t:'Trung tâm dữ liệu',d:'Data Center Tier III chuẩn quốc tế.'},
      {img:'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=700&q=80',tag:'Giáo dục',t:'Trường học thông minh',d:'Số hóa toàn diện 45 trường học.'},
      {img:'https://images.unsplash.com/photo-1556740738-b6a63e27c4df?w=700&q=80',tag:'Bán lẻ',t:'Chuỗi cửa hàng bán lẻ',d:'Hệ thống POS & mạng cho 180 cửa hàng.'},
    ];

    const process = [
      {t:'Tiếp nhận yêu cầu',d:'Lắng nghe và ghi nhận nhu cầu của khách hàng qua hotline hoặc form.'},
      {t:'Khảo sát',d:'Đội ngũ kỹ thuật khảo sát, chẩn đoán chính xác vấn đề.'},
      {t:'Báo giá',d:'Báo giá minh bạch, chi tiết, không phát sinh chi phí ẩn.'},
      {t:'Thực hiện',d:'Tiến hành sửa chữa, triển khai với linh kiện chính hãng.'},
      {t:'Kiểm tra',d:'Kiểm thử kỹ lưỡng, đảm bảo hoạt động ổn định.'},
      {t:'Bàn giao',d:'Bàn giao, hướng dẫn sử dụng và bảo hành dài hạn.'},
    ];

    const testimonials = [
      {img:'https://i.pravatar.cc/120?img=12',n:'Nguyễn Minh Tuấn',r:'Giám đốc IT, FPT Telecom',txt:'TechFix xử lý sự cố hạ tầng cực kỳ nhanh. Đội ngũ chuyên nghiệp, đáng tin cậy.'},
      {img:'https://i.pravatar.cc/120?img=32',n:'Trần Thị Hương',r:'Quản lý văn phòng',txt:'Dịch vụ cứu dữ liệu tuyệt vời, lấy lại được toàn bộ tài liệu quan trọng của công ty.'},
      {img:'https://i.pravatar.cc/120?img=51',n:'Lê Hoàng Nam',r:'CEO Startup',txt:'Lắp đặt camera và mạng cho văn phòng mới rất gọn gàng, thẩm mỹ và ổn định.'},
      {img:'https://i.pravatar.cc/120?img=5',n:'Phạm Thu Trang',r:'Chủ chuỗi cửa hàng',txt:'Hỗ trợ 24/7 thực sự hữu ích. Mỗi khi gặp sự cố là được xử lý ngay lập tức.'},
      {img:'https://i.pravatar.cc/120?img=15',n:'Vũ Đức Anh',r:'Trưởng phòng kỹ thuật',txt:'Hợp tác bảo trì hệ thống CNTT nhiều năm, luôn yên tâm về chất lượng dịch vụ.'},
      {img:'https://i.pravatar.cc/120?img=45',n:'Đỗ Mai Linh',r:'Hiệu trưởng',txt:'Dự án trường học thông minh được triển khai đúng tiến độ, chất lượng vượt mong đợi.'},
    ];

    const pricing = [
      {name:'Basic',desc:'Cho cá nhân & hộ gia đình',price:'299K',per:'/ lần',feat:['Sửa chữa cơ bản','Hỗ trợ qua điện thoại','Bảo hành 1 tháng','Tư vấn miễn phí'],hot:false},
      {name:'Professional',desc:'Cho văn phòng & SME',price:'1.999K',per:'/ tháng',feat:['Tất cả gói Basic','Bảo trì định kỳ','Hỗ trợ từ xa 24/7','Bảo hành 6 tháng','Ưu tiên xử lý'],hot:true},
      {name:'Enterprise',desc:'Cho doanh nghiệp lớn',price:'Liên hệ',per:'',feat:['Tất cả gói Pro','Kỹ thuật viên riêng','SLA cam kết','Bảo hành 12 tháng','Giám sát hạ tầng','Báo cáo định kỳ'],hot:false},
    ];

    const blogs = [
      {img:'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&q=80',cat:'Mẹo hay',t:'10 cách tăng tốc laptop hiệu quả',d:'Những thủ thuật đơn giản giúp máy chạy nhanh hơn.',date:'05/06/2026',read:'5 phút'},
      {img:'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=600&q=80',cat:'Bảo mật',t:'Bảo vệ dữ liệu doanh nghiệp 2026',d:'Chiến lược an ninh mạng toàn diện cho doanh nghiệp.',date:'02/06/2026',read:'7 phút'},
      {img:'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=600&q=80',cat:'Công nghệ',t:'Xu hướng AI trong giám sát',d:'Camera AI thay đổi cách doanh nghiệp vận hành.',date:'28/05/2026',read:'6 phút'},
      {img:'https://images.unsplash.com/photo-1593642702821-c8da6771f0c6?w=600&q=80',cat:'Hướng dẫn',t:'Khi nào nên nâng cấp phần cứng?',d:'Dấu hiệu cho thấy máy của bạn cần nâng cấp.',date:'24/05/2026',read:'4 phút'},
      {img:'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&q=80',cat:'Doanh nghiệp',t:'Tối ưu hạ tầng mạng văn phòng',d:'Thiết kế mạng ổn định cho năng suất tối đa.',date:'20/05/2026',read:'8 phút'},
      {img:'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=600&q=80',cat:'Cứu dữ liệu',t:'Phục hồi dữ liệu ổ cứng hỏng',d:'Quy trình cứu dữ liệu chuyên nghiệp tại TechFix.',date:'16/05/2026',read:'5 phút'},
    ];

    const faqs = [
      {q:'TechFix cung cấp những dịch vụ nào?',a:'Chúng tôi cung cấp đa dịch vụ: sửa chữa máy tính, laptop, điện thoại, máy in, cứu dữ liệu, nâng cấp phần cứng, cài đặt phần mềm, thiết kế mạng, bảo trì CNTT, camera giám sát, website & hosting và hỗ trợ từ xa 24/7.'},
      {q:'Thời gian sửa chữa thường mất bao lâu?',a:'Tùy mức độ phức tạp, đa số lỗi cơ bản được xử lý trong 1-2 giờ. Các dự án lớn sẽ được báo lịch cụ thể sau khi khảo sát.'},
      {q:'TechFix có hỗ trợ tận nơi không?',a:'Có. Chúng tôi hỗ trợ tận nơi tại nhà và doanh nghiệp trên toàn quốc, kèm dịch vụ hỗ trợ từ xa 24/7.'},
      {q:'Chính sách bảo hành như thế nào?',a:'Tùy gói dịch vụ, thời gian bảo hành từ 1 đến 12 tháng. Tất cả linh kiện đều chính hãng và có bảo hành riêng.'},
      {q:'Dữ liệu của tôi có được bảo mật không?',a:'Tuyệt đối. Chúng tôi cam kết bảo mật dữ liệu khách hàng theo tiêu chuẩn ISO và ký thỏa thuận bảo mật (NDA) khi cần.'},
      {q:'Chi phí khảo sát và báo giá có mất phí không?',a:'Hoàn toàn miễn phí. Chúng tôi chỉ tiến hành sau khi khách hàng đồng ý với báo giá minh bạch.'},
      {q:'TechFix có hợp tác bảo trì dài hạn cho doanh nghiệp không?',a:'Có. Gói Professional và Enterprise cung cấp dịch vụ bảo trì định kỳ và giám sát hạ tầng liên tục.'},
      {q:'Tôi có thể thanh toán bằng những hình thức nào?',a:'Chúng tôi hỗ trợ tiền mặt, chuyển khoản, thẻ và ví điện tử. Doanh nghiệp có thể thanh toán theo hợp đồng.'},
      {q:'Cứu dữ liệu có đảm bảo lấy lại 100% không?',a:'Tỷ lệ thành công rất cao tùy tình trạng thiết bị. Chúng tôi kiểm tra miễn phí và chỉ tính phí khi phục hồi thành công.'},
      {q:'Làm sao để liên hệ hỗ trợ nhanh nhất?',a:'Gọi hotline 1900 6868 (24/7), gửi email support@techfix.vn hoặc điền vào form liên hệ — chúng tôi phản hồi trong 15 phút.'},
    ];

    /* =========================================================
       RENDER
    ========================================================= */
    const brandHTML = brands.map(b=>`<span class="brand-logo">${b}</span>`).join('');
    document.getElementById('brandTrack').innerHTML = brandHTML;
    document.getElementById('brandTrack2').innerHTML = brandHTML;

    document.getElementById('servicesGrid').innerHTML = services.map((s,i)=>`
      <article class="service-card glass reveal d${(i%4)+1}">
        <div class="service-ic">${s.ic}</div>
        <h3>${s.t}</h3><p>${s.d}</p>
      </article>`).join('');

    document.getElementById('statsGrid').innerHTML = stats.map((s,i)=>`
      <div class="stat-card glass reveal d${(i%6)+1}">
        <div class="stat-num" data-target="${s.n}" data-suffix="${s.suf}">0</div>
        <div class="stat-label">${s.l}</div>
      </div>`).join('');

    document.getElementById('achieveGrid').innerHTML = achievements.map((a,i)=>`
      <article class="achieve-card glass reveal zoom d${(i%5)+1}">
        <div class="achieve-ic">${a.ic}</div><h4>${a.t}</h4><p>${a.d}</p>
      </article>`).join('');

    document.getElementById('projectsGrid').innerHTML = projects.map((p,i)=>`
      <article class="project-card reveal d${(i%3)+1}">
        <img src="${p.img}" alt="${p.t}" loading="lazy" />
        <div class="project-overlay">
          <span class="tag">${p.tag}</span>
          <h3>${p.t}</h3><p>${p.d}</p>
        </div>
      </article>`).join('');

    document.getElementById('timeline').innerHTML = process.map((p,i)=>`
      <div class="tl-item reveal ${i%2===0?'left':'right'}">
        <div class="tl-content glass"><h4>${p.t}</h4><p>${p.d}</p></div>
        <div class="tl-dot">${i+1}</div>
        <div class="tl-spacer"></div>
      </div>`).join('');

    document.getElementById('testiGrid').innerHTML = testimonials.map((t,i)=>`
      <article class="testi-card glass reveal d${(i%3)+1}">
        <div class="testi-stars">★★★★★</div>
        <p class="testi-text">"${t.txt}"</p>
        <div class="testi-author">
          <img src="${t.img}" alt="${t.n}" loading="lazy" />
          <div><strong>${t.n}</strong><span>${t.r}</span></div>
        </div>
      </article>`).join('');

    document.getElementById('pricingGrid').innerHTML = pricing.map(p=>`
      <div class="price-card glass ${p.hot?'featured':''} reveal">
        ${p.hot?'<span class="price-badge">Phổ biến nhất</span>':''}
        <h3>${p.name}</h3>
        <p class="desc">${p.desc}</p>
        <div class="price-amount">${p.price}<small>${p.per}</small></div>
        <ul class="price-features">
          ${p.feat.map(f=>`<li><span class="chk">✓</span> ${f}</li>`).join('')}
        </ul>
        <a href="#contact" class="btn ${p.hot?'btn-ghost':'btn-primary'}" style="width:100%;${p.hot?'background:#fff;color:#4f46e5;':''}">Đăng ký ngay</a>
      </div>`).join('');

    document.getElementById('blogGrid').innerHTML = blogs.map((b,i)=>`
      <article class="blog-card glass reveal d${(i%3)+1}">
        <div class="blog-thumb"><img src="${b.img}" alt="${b.t}" loading="lazy" /></div>
        <div class="blog-body">
          <span class="blog-cat">${b.cat}</span>
          <h3>${b.t}</h3><p>${b.d}</p>
          <div class="blog-meta"><span>📅 ${b.date}</span><span>⏱️ ${b.read}</span></div>
        </div>
      </article>`).join('');

    document.getElementById('faqList').innerHTML = faqs.map(f=>`
      <div class="faq-item glass reveal">
        <button class="faq-q" aria-expanded="false">${f.q}<span class="plus">+</span></button>
        <div class="faq-a"><p>${f.a}</p></div>
      </div>`).join('');

    /* =========================================================
       LOADING SCREEN
    ========================================================= */
    window.addEventListener('load',()=>{
      setTimeout(()=>document.getElementById('loader').classList.add('hidden'),700);
    });

    /* =========================================================
       NAVBAR + HAMBURGER
    ========================================================= */
    const navbar = document.getElementById('navbar');
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');

    window.addEventListener('scroll',()=>{
      navbar.classList.toggle('scrolled', window.scrollY > 30);
      document.getElementById('backTop').classList.toggle('show', window.scrollY > 500);
      document.querySelectorAll('.orb').forEach((orb,i)=>{
        orb.style.transform = `translateY(${window.scrollY*(0.05+i*0.03)}px)`;
      });
    });

    hamburger.addEventListener('click',()=>{
      const open = mobileMenu.classList.toggle('open');
      hamburger.setAttribute('aria-expanded', open);
    });
    mobileMenu.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>{
      mobileMenu.classList.remove('open');
      hamburger.setAttribute('aria-expanded','false');
    }));

    /* SMOOTH SCROLL */
    document.querySelectorAll('a[href^="#"]').forEach(link=>{
      link.addEventListener('click',e=>{
        const target = document.querySelector(link.getAttribute('href'));
        if(target){ e.preventDefault(); target.scrollIntoView({behavior:'smooth'}); }
      });
    });

    document.getElementById('backTop').addEventListener('click',()=>{
      window.scrollTo({top:0,behavior:'smooth'});
    });

    /* =========================================================
       FAQ ACCORDION
    ========================================================= */
    document.querySelectorAll('.faq-q').forEach(btn=>{
      btn.addEventListener('click',()=>{
        const item = btn.parentElement;
        const ans = item.querySelector('.faq-a');
        const isActive = item.classList.contains('active');
        document.querySelectorAll('.faq-item').forEach(it=>{
          it.classList.remove('active');
          it.querySelector('.faq-a').style.maxHeight = null;
          it.querySelector('.faq-q').setAttribute('aria-expanded','false');
        });
        if(!isActive){
          item.classList.add('active');
          ans.style.maxHeight = ans.scrollHeight+'px';
          btn.setAttribute('aria-expanded','true');
        }
      });
    });

    /* =========================================================
       SCROLL REVEAL
    ========================================================= */
    const revealObs = new IntersectionObserver(entries=>{
      entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('in'); revealObs.unobserve(e.target); } });
    },{threshold:0.12});
    document.querySelectorAll('.reveal').forEach(el=>revealObs.observe(el));

    /* =========================================================
       COUNTER ANIMATION
    ========================================================= */
    function formatNum(n){ return n.toLocaleString('vi-VN'); }
    const counterObs = new IntersectionObserver(entries=>{
      entries.forEach(e=>{
        if(e.isIntersecting){
          const el = e.target;
          const target = +el.dataset.target;
          const suffix = el.dataset.suffix || '';
          const duration = 1800; const start = performance.now();
          function tick(now){
            const p = Math.min((now-start)/duration,1);
            const eased = 1-Math.pow(1-p,3);
            el.textContent = formatNum(Math.floor(eased*target))+suffix;
            if(p<1) requestAnimationFrame(tick);
            else el.textContent = formatNum(target)+suffix;
          }
          requestAnimationFrame(tick);
          counterObs.unobserve(el);
        }
      });
    },{threshold:0.4});
    document.querySelectorAll('.stat-num').forEach(el=>counterObs.observe(el));

    /* =========================================================
       FLOATING PARTICLES
    ========================================================= */
    (function(){
      const wrap = document.getElementById('particles');
      const colors = ['#38bdf8','#818cf8','#c084fc','#06b6d4'];
      for(let i=0;i<28;i++){
        const p = document.createElement('span');
        p.className = 'particle';
        const size = Math.random()*6+3;
        p.style.left = Math.random()*100+'vw';
        p.style.width = p.style.height = size+'px';
        p.style.background = colors[Math.floor(Math.random()*colors.length)];
        p.style.animationDuration = (Math.random()*12+10)+'s';
        p.style.animationDelay = (Math.random()*10)+'s';
        wrap.appendChild(p);
      }
    })();

    /* =========================================================
       VOICE SEARCH (từ file PHP cũ)
    ========================================================= */
    function startVoiceSearch(){
      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
      if(!SpeechRecognition) return alert('Trình duyệt không hỗ trợ nhận diện giọng nói.');
      const recognition = new SpeechRecognition();
      recognition.lang = 'vi-VN';
      document.getElementById('voiceOverlay').style.display = 'flex';
      document.getElementById('voiceStatus').innerText = 'Đang nghe...';
      recognition.start();
      recognition.onresult = (event)=>{
        const transcript = event.results[0][0].transcript;
        document.getElementById('voiceStatus').innerText = `Nhận diện: "${transcript}"`;
        setTimeout(()=>{
          window.location.href = `/TechFixPHP/Customer/Service.php?search=${encodeURIComponent(transcript)}`;
        },800);
      };
      recognition.onerror = ()=>closeVoiceSearch();
      recognition.onend = ()=>{
        if(document.getElementById('voiceOverlay').style.display === 'flex'){
          closeVoiceSearch();
        }
      };
    }
    function closeVoiceSearch(){
      document.getElementById('voiceOverlay').style.display = 'none';
    }

    /* =========================================================
       SEARCH SUGGESTION (từ file PHP cũ)
    ========================================================= */
    const searchInput = document.getElementById('voiceSearchInput');
    const resultBox = document.getElementById('search-results');
    if(searchInput){
      let debounceTimer;
      searchInput.addEventListener('input',function(){
        clearTimeout(debounceTimer);
        const keyword = this.value.trim();
        if(keyword.length < 1){ resultBox.style.display='none'; return; }
        debounceTimer = setTimeout(()=>{
          fetch(`/TechFixPHP/pages/api/search_suggest.php?keyword=${encodeURIComponent(keyword)}`)
            .then(res=>res.json())
            .then(data=>{
              resultBox.innerHTML = '';
              if(data.length > 0){
                resultBox.style.display = 'block';
                data.forEach(s=>{
                  resultBox.innerHTML += `
                    <a href="/TechFixPHP/pages/public_page/service_detail.php?id=${s.id}" class="suggestion-item">
                      <img src="/TechFixPHP/${s.image}" class="suggestion-img"
                           onerror="this.src='/TechFixPHP/assets/image/default.png'" alt="${s.name}">
                      <div>
                        <div style="font-weight:600; font-size:13px; color:#fff;">${s.name}</div>
                        <div style="color:var(--sky); font-size:11px;">${s.price}</div>
                      </div>
                    </a>`;
                });
              } else {
                resultBox.style.display = 'none';
              }
            })
            .catch(()=>{ resultBox.style.display='none'; });
        },300);
      });

      // Đóng suggestion khi click ra ngoài
      document.addEventListener('click',function(e){
        if(!searchInput.contains(e.target) && !resultBox.contains(e.target)){
          resultBox.style.display = 'none';
        }
      });
    }

    /* =========================================================
       CONTACT FORM VALIDATION
    ========================================================= */
    const form = document.getElementById('contactForm');
    const formMsg = document.getElementById('formMsg');

    function setError(id, hasError){
      document.getElementById(id).closest('.field').classList.toggle('invalid', hasError);
    }

    form.addEventListener('submit',(e)=>{
      e.preventDefault();
      const name    = document.getElementById('cf-name').value.trim();
      const email   = document.getElementById('cf-email').value.trim();
      const phone   = document.getElementById('cf-phone').value.trim();
      const service = document.getElementById('cf-service').value;
      const message = document.getElementById('cf-message').value.trim();

      const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      const phoneRe = /^[0-9]{10,11}$/;

      const eName    = name.length < 2;
      const eEmail   = !emailRe.test(email);
      const ePhone   = !phoneRe.test(phone.replace(/\s/g,''));
      const eService = service === '';
      const eMsg     = message.length < 10;

      setError('cf-name',    eName);
      setError('cf-email',   eEmail);
      setError('cf-phone',   ePhone);
      setError('cf-service', eService);
      setError('cf-message', eMsg);

      if(eName||eEmail||ePhone||eService||eMsg){ formMsg.classList.remove('show'); return; }

      formMsg.classList.add('show');
      form.reset();
      setTimeout(()=>formMsg.classList.remove('show'),6000);
    });

    form.querySelectorAll('input,select,textarea').forEach(el=>{
      el.addEventListener('input',()=>el.closest('.field').classList.remove('invalid'));
    });

    /* =========================================================
       PWA — Install App button (từ file PHP cũ)
    ========================================================= */
    let deferredPrompt;
    window.addEventListener('beforeinstallprompt',(e)=>{
      e.preventDefault();
      deferredPrompt = e;
      const installBtn = document.getElementById('install-app-btn');
      if(installBtn) installBtn.style.display = 'block';
    });
    const installBtn = document.getElementById('install-app-btn');
    if(installBtn){
      installBtn.addEventListener('click',async()=>{
        if(deferredPrompt){
          deferredPrompt.prompt();
          await deferredPrompt.userChoice;
          deferredPrompt = null;
          installBtn.style.display = 'none';
        }
      });
    }
  </script>
</body>
</html>