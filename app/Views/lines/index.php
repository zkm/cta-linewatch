<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= esc($title ?? 'CTA Lines') ?></title>
  <style>
    body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background:#0b132b; color:#f7f9fb; }
    header { padding:1rem; background:#0d1b2a; }
    main { padding:1rem; max-width:960px; margin:0 auto; }
    .grid { display:grid; gap:.75rem; }
    @media (min-width: 720px) { .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    .card { background:#1c2541; border:1px solid #2a4365; border-radius:.6rem; padding:1rem; }
    a { color:#9be4e1; text-decoration:none; }
    a:hover { text-decoration:underline; }
    .muted { color:#cde3e2; }

    /* CTA brand colors */
    .line-card { border-left: 10px solid transparent; }
    .line-blue { border-left-color: #00a1de; }
    .line-brown { border-left-color: #62361b; }
    .line-green { border-left-color: #009b3a; }
    .line-orange { border-left-color: #f9461c; }
    .line-purple { border-left-color: #522398; }
    .line-pink { border-left-color: #e27ea6; }
    .line-red { border-left-color: #c60c30; }
    .line-yellow { border-left-color: #f9e300; }
  </style>
</head>
<body>
  <header><h1 style="margin:0">CTA Train Lines</h1></header>
  <main>
    <p class="muted">Pick a line to see stations, then arrivals.</p>
    <div class="grid">
      <?php foreach ($lines as $l): ?>
        <div class="card line-card line-<?= esc($l['slug']) ?>">
          <h2 style="margin-top:0"><a href="<?= site_url('lines/' . $l['slug']) ?>"><?= esc($l['name']) ?> Line</a></h2>
          <p class="muted"><?= esc($l['desc']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </main>
</body>
</html>
