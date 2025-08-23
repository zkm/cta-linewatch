<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= esc($title ?? 'CTA Train Arrivals') ?></title>
    <meta name="description" content="Accessible CTA Train Arrivals viewer" />
    <style>
        :root {
            --bg: #0b132b;
            --card: #1c2541;
            --muted: #5bc0be;
            --text: #f7f9fb;
            --warn: #ffcc00;
            --danger: #ff6b6b;
            --ok: #4cd964;
        }
        body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background: var(--bg); color: var(--text); }
        header { padding: 1rem; background: #0d1b2a; }
        main { padding: 1rem; }
        .container { max-width: 960px; margin: 0 auto; }
        .card { background: var(--card); border-radius: 10px; padding: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,.2); }
        form { display: grid; grid-template-columns: 1fr auto; gap: .5rem; align-items: end; margin-bottom: 1rem; }
        label { display:block; font-weight: 600; margin-bottom: .25rem; }
        input[type="text"] { width: 100%; padding: .6rem .7rem; border-radius: .5rem; border: 1px solid #314e73; background: #0e2440; color: var(--text); }
        button { padding: .65rem .9rem; border: 0; border-radius: .5rem; background: #247ba0; color: #fff; font-weight: 700; cursor: pointer; }
        button:hover { background: #2a9d8f; }
        .helper { color: #cde3e2; font-size: .9rem; }
        .muted { color: var(--muted); }
        .sr-only { position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); border:0; }
        .grid { display: grid; gap: .75rem; }
        @media (min-width: 640px) { .grid { grid-template-columns: repeat(2, minmax(0,1fr)); } }
        @media (min-width: 960px) { .grid { grid-template-columns: repeat(3, minmax(0,1fr)); } }
        .arrival { border: 1px solid #2a4365; border-radius: .6rem; padding: .75rem; background: #102a43; }
        .arrival header { display:flex; justify-content: space-between; align-items: baseline; padding:0; background: transparent; }
    .badge { display:inline-block; padding: .15rem .45rem; border-radius: .35rem; font-size: .85rem; font-weight: 700; }
    .route { background:#264653; color:#e9c46a; }
        .due { background: var(--warn); color: #1a1a1a; }
        .delayed { background: var(--danger); color:#fff; }
        .approaching { background: var(--ok); color:#1a1a1a; }
        .empty { padding: 1rem; background: #0e2440; border: 1px dashed #375b86; border-radius: .6rem; }
        a.skip-link { position: absolute; left: -999px; top: -999px; }
        a.skip-link:focus { left: .5rem; top: .5rem; background: #fff; color:#000; padding:.5rem; border-radius:.25rem; }

    /* CTA route colors */
    .rt-Red { background:#c60c30; color:#fff; }
    .rt-Blu, .rt-Blue { background:#00a1de; color:#00131a; }
    .rt-Brn { background:#62361b; color:#fff; }
    .rt-G, .rt-Grn, .rt-Green { background:#009b3a; color:#001a0a; }
    .rt-Org, .rt-Orange { background:#f9461c; color:#1a0000; }
    .rt-P, .rt-Purple { background:#522398; color:#fff; }
    .rt-Pnk, .rt-Pink { background:#e27ea6; color:#1a0014; }
    .rt-Y, .rt-Yellow { background:#f9e300; color:#1a1700; }
    </style>
    <link rel="icon" href="/favicon.ico" />
    <meta name="theme-color" content="#0b132b" />
    <meta name="color-scheme" content="dark light" />
    <meta name="supported-color-schemes" content="dark light" />
    <meta name="format-detection" content="telephone=no" />
</head>
<body>
    <a href="#content" class="skip-link">Skip to content</a>
    <header role="banner">
        <div class="container">
            <h1 id="page-title" style="margin:.2rem 0">CTA Train Arrivals</h1>
            <p class="helper" aria-live="polite">
                <?= $hasKey ? 'API key detected.' : 'No API key; set CTA_TRAIN_API_KEY in .env' ?>
            </p>
        </div>
    </header>
    <main id="content" role="main" class="container">
        <section class="card" aria-labelledby="search-title">
            <h2 id="search-title" style="margin-top:0">Find a Station</h2>
            <form role="search" method="get" action="<?= site_url('arrivals') ?>" aria-describedby="search-help">
                <div>
                    <label for="mapid">Station Map ID</label>
                    <input id="mapid" name="mapid" inputmode="numeric" pattern="[0-9]+" value="<?= esc($mapid) ?>" aria-describedby="search-help" />
                    <div id="search-help" class="helper">Enter CTA station map ID (e.g., 40380 Clark/Lake). Not sure? Try 40380.</div>
                </div>
                <div>
                    <button type="submit">Show Arrivals</button>
                </div>
            </form>

            <?php if (($result['ok'] ?? null) === true): ?>
                <p class="muted" aria-live="polite">Last updated: <?= esc($result['updated'] ?? '') ?> at station <?= esc($result['station']['name'] ?? '') ?>.</p>

                <?php if (empty($result['arrivals'])): ?>
                    <div class="empty" role="status">No upcoming trains returned.</div>
                <?php else: ?>
                    <div class="grid" role="list" aria-label="Upcoming trains">
                        <?php foreach ($result['arrivals'] as $a): ?>
                            <article class="arrival" role="listitem" aria-labelledby="run-<?= esc($a['run']) ?>">
                                <header>
                                    <div>
                                        <?php
                                            $code = $a['route'];
                                            $cls = 'route';
                                            // CTA API routes can be short codes like G, Org, Brn, P, Y, Red, Blue
                                            $map = [
                                                'Red' => 'rt-Red', 'RED' => 'rt-Red',
                                                'Blue' => 'rt-Blue', 'Blu' => 'rt-Blue', 'BLUE' => 'rt-Blue',
                                                'Brn' => 'rt-Brn', 'Brown' => 'rt-Brn',
                                                'G' => 'rt-G', 'Grn' => 'rt-G', 'Green' => 'rt-G',
                                                'Org' => 'rt-Orange', 'Orange' => 'rt-Orange',
                                                'P' => 'rt-P', 'Purple' => 'rt-P',
                                                'Pnk' => 'rt-Pink', 'Pink' => 'rt-Pink',
                                                'Y' => 'rt-Yellow', 'Yellow' => 'rt-Yellow',
                                            ];
                                            if (isset($map[$code])) { $cls = $map[$code]; }
                                        ?>
                                        <span class="badge <?= esc($cls) ?>" aria-label="Route"><?= esc($a['route']) ?></span>
                                        <strong id="run-<?= esc($a['run']) ?>">to <?= esc($a['dest']) ?></strong>
                                    </div>
                                    <time datetime="<?= esc($a['arrives']) ?>">
                                        <?= esc(date('g:i A', strtotime($a['arrives']))) ?>
                                    </time>
                                </header>
                                <p class="helper" style="margin:.25rem 0 .5rem 0">Platform: <?= esc($a['platform']) ?></p>
                                <p>
                                    <?php if ($a['flags']['approaching']): ?>
                                        <span class="badge approaching">Approaching</span>
                                    <?php endif; ?>
                                    <?php if ($a['flags']['delayed']): ?>
                                        <span class="badge delayed">Delayed</span>
                                    <?php endif; ?>
                                    <?php if ($a['flags']['scheduled']): ?>
                                        <span class="badge due">Scheduled</span>
                                    <?php endif; ?>
                                </p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php elseif (isset($result['ok']) && $result['ok'] === false): ?>
                <div class="empty" role="alert">Error: <?= esc($result['error'] ?? 'Unknown error') ?></div>
            <?php else: ?>
                <p class="helper">Enter a station map ID to see arrivals.</p>
            <?php endif; ?>
        </section>
        <p class="helper" style="margin-top:1rem">This page is designed for WCAG 2.1 AA: semantic headings and landmarks, accessible form labels, visible focus, sufficient contrast, and reduced motion.</p>
    </main>
</body>
</html>
