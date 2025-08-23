<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= esc($title ?? 'Stations') ?></title>
  <style>
    body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background:#0b132b; color:#f7f9fb; }
    header { padding:1rem; background:#0d1b2a; }
    main { padding:1rem; max-width:960px; margin:0 auto; }
    .grid { display:grid; gap:.5rem; }
    @media (min-width: 680px) { .grid { grid-template-columns: repeat(2, minmax(0,1fr)); } }
    @media (min-width: 980px) { .grid { grid-template-columns: repeat(3, minmax(0,1fr)); } }
    .card { background:#1c2541; border:1px solid #2a4365; border-radius:.6rem; padding:.75rem; }
    a { color:#9be4e1; text-decoration:none; }
    a:hover { text-decoration:underline; }
    .muted { color:#cde3e2; }

    /* CTA colors */
    .line-accent { border-left: 10px solid; padding-left: .75rem; }
    .blue { --line:#00a1de; }
    .brown { --line:#62361b; }
    .green { --line:#009b3a; }
    .orange { --line:#f9461c; }
    .purple { --line:#522398; }
    .pink { --line:#e27ea6; }
    .red { --line:#c60c30; }
    .yellow { --line:#f9e300; }
    .has-line { border-left: 10px solid var(--line); }
  </style>
</head>
<body>
  <header class="<?= esc($line) ?>">
    <h1 class="line-accent" style="margin:0; border-left-color: var(--line)"><?= ucfirst(esc($line)) ?> Line Stations</h1>
    <p class="muted"><a href="<?= site_url('lines') ?>">Back to lines</a></p>
  </header>
  <main>
    <?php if (empty($stations)): ?>
      <p class="muted">No stations found. Ensure legacy tables exist and are populated.</p>
    <?php else: ?>
    <div class="grid <?= esc($line) ?>">
        <?php foreach ($stations as $s): ?>
      <div class="card has-line">
            <strong><?= esc($s['station']) ?></strong>
            <div class="muted">Map ID: <?= esc($s['sid']) ?></div>
            <p style="margin:.5rem 0 0"><a href="<?= site_url('arrivals?mapid=' . $s['sid']) ?>">View arrivals</a></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
