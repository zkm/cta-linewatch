<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= esc($title ?? 'Stations') ?></title>
  <style>
    :root { color-scheme: dark; }
    body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background:#0b132b; color:#f7f9fb; }
    header { padding:1rem; background:#0d1b2a; }
    main { padding:1rem; max-width:960px; margin:0 auto; }
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

  /* Diagram */
  .diagram { display:block; }
  .stops { position:relative; border-left:10px solid var(--line); margin-left:16px; }
  .stop { position:relative; padding:1rem; border-bottom:1px solid #2a4365; }
    .stop:last-child { border-bottom:0; }
  .dot { position:absolute; left:-16px; top:calc(50% - 6px); width:12px; height:12px; background:#f7f9fb; border:3px solid var(--line); border-radius:50%; box-shadow:0 0 0 2px #1c2541; }
    .stop-name { font-weight:600; }
    .card { background:#1c2541; border:1px solid #2a4365; border-radius:.6rem; padding:1rem; }
  .controls { display:flex; gap:.75rem; align-items:center; margin:.75rem 0; }
  .controls input { flex:1; padding:.5rem .6rem; border-radius:.4rem; border:1px solid #2a4365; background:#0d1b2a; color:#f7f9fb; }
  .btn { padding:.5rem .7rem; border:1px solid #2a4365; background:#0d1b2a; color:#f7f9fb; border-radius:.4rem; cursor:pointer; }
  .btn:hover { background:#132033; }
  </style>
</head>
<body>
  <header class="<?= esc($line) ?>">
    <h1 class="line-accent" style="margin:0; border-left-color: var(--line)"><?= ucfirst(esc($line)) ?> Line Stations</h1>
    <p class="muted"><a href="<?= site_url('lines') ?>">Back to lines</a></p>
  </header>
  <main>
    <?php if (empty($stations)): ?>
      <p class="muted">No stations found. Provide <code>stations/<?= esc($line) ?>.json</code> (preferred) or run DB migrations and populate the <code><?= esc($line) ?></code> table.</p>
    <?php else: ?>
      <div class="card <?= esc($line) ?>">
        <div class="controls">
          <input id="filter" type="search" placeholder="Filter stations..." aria-label="Filter stations" />
          <button id="reverse" class="btn" type="button" aria-pressed="false" title="Reverse order">Reverse</button>
          <span class="muted" id="count"></span>
        </div>
        <div class="diagram">
          <div class="stops">
            <?php foreach ($stations as $idx => $s): ?>
              <div class="stop" data-index="<?= (int) $idx ?>" data-name="<?= strtolower(esc($s['station'])) ?>">
                <span class="dot" aria-hidden="true"></span>
                <div class="stop-name"><a href="<?= site_url('arrivals?mapid=' . $s['sid']) ?>"><?= esc($s['station']) ?></a></div>
                <div class="muted">Map ID: <?= esc($s['sid']) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php if (($line ?? '') === 'brown'): ?>
          <p class="muted" style="margin:.75rem 0 0">Note: South of Merchandise Mart, Brown Line trains loop around downtown (The Loop). Some Loop stops may be skipped depending on direction.</p>
        <?php endif; ?>
      </div>
      <script>
        const filter = document.querySelector('#filter');
        const reverseBtn = document.querySelector('#reverse');
        const stopsContainer = document.querySelector('.stops');
        let stops = Array.from(document.querySelectorAll('.stop'));
        const count = document.querySelector('#count');
        function update() {
          const q = (filter.value || '').trim().toLowerCase();
          let shown = 0;
          stops.forEach(el => {
            const match = !q || el.dataset.name.includes(q);
            el.style.display = match ? '' : 'none';
            if (match) shown++;
          });
          count.textContent = shown + ' of ' + stops.length;
        }
        filter?.addEventListener('input', update);
        reverseBtn?.addEventListener('click', () => {
          const pressed = reverseBtn.getAttribute('aria-pressed') === 'true';
          reverseBtn.setAttribute('aria-pressed', String(!pressed));
          // Reorder DOM nodes
          const ordered = Array.from(stops).sort((a, b) => {
            const ia = parseInt(a.dataset.index || '0', 10);
            const ib = parseInt(b.dataset.index || '0', 10);
            return pressed ? ia - ib : ib - ia;
          });
          ordered.forEach(node => stopsContainer.appendChild(node));
          // Update current stops reference
          stops = Array.from(document.querySelectorAll('.stop'));
          update();
        });
        update();
      </script>
    <?php endif; ?>
  </main>
</body>
</html>
