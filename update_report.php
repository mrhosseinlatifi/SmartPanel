<?php
define('maindir', __DIR__);
require maindir . '/config.php';

// ── validate key ─────────────────────────────────────────────────────────────
$key = $_GET['k'] ?? '';
if (!preg_match('/^[a-f0-9]{32}$/', $key)) {
    http_response_code(404);
    exit;
}

$log_file = ROOTPATH . '/temp/ul_' . $key . '.json';
if (!file_exists($log_file)) {
    http_response_code(404);
    exit;
}

$d = json_decode(file_get_contents($log_file), true);
if (!$d) {
    http_response_code(500);
    exit;
}

// ── data extraction ───────────────────────────────────────────────────────────
$type          = (int) ($d['type'] ?? 0);
$type_label    = htmlspecialchars($d['type_label'] ?? 'نامشخص');
$api           = htmlspecialchars($d['api'] ?? '');
$ts            = (int) ($d['timestamp'] ?? 0);
$add_count     = (int) ($d['add_count'] ?? 0);
$off_count     = (int) ($d['off_count'] ?? 0);
$category      = htmlspecialchars($d['category'] ?? '');
$category_new  = (bool) ($d['category_new'] ?? false);
$settings      = $d['settings'] ?? [];
$disabled      = $d['disabled_products'] ?? [];
$products_list = $d['products'] ?? [];

$date_str = $ts ? date('Y/m/d', $ts) . ' — ' . date('H:i:s', $ts) : '—';

// ── type icon ─────────────────────────────────────────────────────────────────
$icons = [2 => '🛍', 5 => '📦', 6 => '🔄', 7 => '📂'];
$icon  = $icons[$type] ?? '📋';

// ── settings rows (type 6) ────────────────────────────────────────────────────
$settings_html = '';
if ($type === 6 && !empty($settings)) {
    $rows = [
        'name'    => 'بروزرسانی نام',
        'price'   => 'بروزرسانی قیمت',
        'min'     => 'بروزرسانی محدوده + وضعیت',
        'info'    => 'بروزرسانی توضیحات',
        'convert' => 'تبدیل دلار به تومان',
    ];
    $tbr = '';
    foreach ($rows as $k => $label) {
        $on  = !empty($settings[$k]);
        $b   = $on
            ? '<span class="badge on">✔ فعال</span>'
            : '<span class="badge off">✘ غیرفعال</span>';
        $tbr .= "<tr><td>{$label}</td><td>{$b}</td></tr>";
    }
    if (!empty($settings['price'])) {
        $pt   = (($settings['price_type'] ?? 1) == 1) ? 'فقط افزایش 📈' : 'همیشه بروز 🔄';
        $tbr .= "<tr><td>نوع قیمت‌گذاری</td><td><span class='badge info'>{$pt}</span></td></tr>";
    }
    if (!empty($settings['up'])) {
        $tbr .= '<tr><td>درصد افزایش قیمت</td><td>' . htmlspecialchars((string) $settings['up']) . '%</td></tr>';
    }
    if (!empty($settings['round'])) {
        $tbr .= '<tr><td>رند کردن</td><td>' . htmlspecialchars((string) $settings['round']) . '</td></tr>';
    }
    $settings_html = "
    <div class='card'>
      <h2>⚙️ تنظیمات استفاده‌شده</h2>
      <table class='data-table'><tbody>{$tbr}</tbody></table>
    </div>";
}

// ── disabled products (type 6) ────────────────────────────────────────────────
$disabled_html = '';
if (!empty($disabled)) {
    $rows2 = '';
    foreach ($disabled as $i => $p) {
        $pname   = htmlspecialchars((string) ($p['name'] ?? '—'));
        $service = htmlspecialchars((string) ($p['service'] ?? '—'));
        $rows2  .= '<tr><td>' . ($i + 1) . "</td><td>{$service}</td><td class='col-name'>{$pname}</td></tr>";
    }
    $disabled_html = "
    <div class='card danger'>
      <h2>⛔ محصولات غیرفعال‌شده (" . count($disabled) . ")</h2>
      <p class='note'>این محصولات در وب‌سرویس یافت نشدند و به همین دلیل غیرفعال شدند.</p>
      <table class='data-table'>
        <thead><tr><th>#</th><th>سرویس</th><th>نام محصول</th></tr></thead>
        <tbody>{$rows2}</tbody>
      </table>
    </div>";
}

// ── category info (type 7) ────────────────────────────────────────────────────
$category_html = '';
if ($type === 7 && $category !== '') {
    $new_badge = $category_new
        ? "<span class='badge on'>دسته جدید ایجاد شد</span>"
        : "<span class='badge info'>دسته از قبل موجود بود</span>";
    $category_html = "
    <div class='card'>
      <h2>📂 دسته‌بندی</h2>
      <table class='data-table'><tbody>
        <tr><td>نام دسته</td><td><strong>{$category}</strong></td></tr>
        <tr><td>وضعیت</td><td>{$new_badge}</td></tr>
      </tbody></table>
    </div>";
}

// ── summary stats ─────────────────────────────────────────────────────────────
$stats = "<div class='stats'>";
$stats .= "<div class='stat green'><div class='stat-num'>{$add_count}</div><div class='stat-lbl'>" . ($type === 6 ? 'محصول بروز شد' : 'محصول اضافه شد') . "</div></div>";
if ($type === 6) {
    $stats .= "<div class='stat red'><div class='stat-num'>{$off_count}</div><div class='stat-lbl'>محصول غیرفعال شد</div></div>";
}
if (!empty($products_list)) {
    $stats .= "<div class='stat blue'><div class='stat-num'>" . count($products_list) . "</div><div class='stat-lbl'>در جدول گزارش</div></div>";
}
$stats .= "</div>";

// ── products table ────────────────────────────────────────────────────────────
$products_html = '';
if (!empty($products_list)) {
    $is_update = ($type === 6);
    $has_usd   = false;
    foreach ($products_list as $p) {
        if (!empty($p['price_usd'])) { $has_usd = true; break; }
    }

    $has_unit = false;
    foreach ($products_list as $p) {
        if (isset($p['unit_price'])) { $has_unit = true; break; }
    }
    $has_cost = false;
    foreach ($products_list as $p) {
        if (isset($p['cost_price'])) { $has_cost = true; break; }
    }

    $rows_html = '';
    foreach ($products_list as $i => $p) {
        $pname    = htmlspecialchars((string) ($p['name'] ?? '—'));
        $service  = htmlspecialchars((string) ($p['service'] ?? '—'));
        $min      = (int) ($p['min'] ?? 0);
        $max      = (int) ($p['max'] ?? 0);
        $price_usd = (float) ($p['price_usd'] ?? 0);

        $cost_cell = $has_cost ? ('<td>' . number_format((float) ($p['cost_price'] ?? 0)) . '</td>') : '';

        if ($is_update) {
            $old_p = number_format((float) ($p['old_price'] ?? 0));
            $new_p = number_format((float) ($p['new_price'] ?? 0));
            $changed = ($p['old_price'] ?? 0) != ($p['new_price'] ?? 0);
            $price_cells = "<td>{$old_p}</td><td class='" . ($changed ? 'price-changed' : '') . "'>{$new_p}</td>";
        } else {
            $price_cells = "<td>" . number_format((float) ($p['price'] ?? 0)) . "</td>";
        }

        $unit_cell = '';
        if ($has_unit) {
            $uv = (float) ($p['unit_price'] ?? 0);
            if (floor($uv) == $uv) {
                $unit_txt = number_format($uv);
            } else {
                $unit_txt = rtrim(rtrim(number_format($uv, 3), '0'), '.');
            }
            $unit_cell = "<td class='col-unit'>{$unit_txt}</td>";
        }

        $usd_cell = $has_usd ? ('<td>' . ($price_usd > 0 ? '$' . number_format($price_usd, 4) : '—') . '</td>') : '';

        $rows_html .= "<tr data-idx='" . ($i + 1) . "' data-name='" . htmlspecialchars(strtolower((string) ($p['name'] ?? '')), ENT_QUOTES) . "'>";
        $rows_html .= "<td class='row-num'>" . ($i + 1) . "</td>";
        $rows_html .= "<td class='col-name'>{$pname}</td>";
        $rows_html .= "<td class='col-service'>{$service}</td>";
        $rows_html .= $cost_cell;
        $rows_html .= $price_cells;
        $rows_html .= $unit_cell;
        $rows_html .= $usd_cell;
        $rows_html .= "<td>{$min}</td><td>{$max}</td>";
        $rows_html .= "</tr>\n";
    }

    $cost_header = $has_cost ? '<th>قیمت سایت</th>' : '';
    if ($is_update) {
        $price_headers = "<th>فروش قبلی</th><th>فروش جدید</th>";
    } else {
        $price_headers = "<th>قیمت فروش</th>";
    }
    $unit_header = $has_unit ? '<th>هزینه هر واحد</th>' : '';
    $usd_header = $has_usd ? '<th>قیمت دلاری</th>' : '';

    $total = count($products_list);
    $products_html = "
    <div class='card' id='products-card'>
      <div class='products-header'>
        <h2>📦 جزئیات محصولات <span class='total-badge'>{$total}</span></h2>
        <input type='search' id='search-box' placeholder='🔍 جستجوی نام یا سرویس...' oninput='filterTable(this.value)' autocomplete='off'>
      </div>
      <p class='note'>«قیمت سایت» نرخ وب‌سرویس و «قیمت فروش» نرخ ثبت‌شده در ربات است. «هزینه هر واحد» قیمت هر یک عدد است که دقیقاً با همان تابع محاسبهٔ ربات هنگام فروش به‌دست می‌آید.</p>
      <div class='table-wrap'>
        <table class='data-table' id='products-table'>
          <thead>
            <tr>
              <th class='col-num'>#</th>
              <th class='col-name'>نام محصول</th>
              <th class='col-service'>سرویس</th>
              {$cost_header}
              {$price_headers}
              {$unit_header}
              {$usd_header}
              <th>حداقل</th>
              <th>حداکثر</th>
            </tr>
          </thead>
          <tbody id='table-body'>{$rows_html}</tbody>
        </table>
        <div id='no-results' class='no-results' style='display:none'>نتیجه‌ای یافت نشد</div>
      </div>
      <div class='pagination-bar' id='pagination-bar'>
        <button class='pg-btn' id='pg-prev' onclick='changePage(-1)'>&#8250; قبلی</button>
        <span id='pg-info'></span>
        <button class='pg-btn' id='pg-next' onclick='changePage(1)'>بعدی &#8249;</button>
      </div>
    </div>";
}

?><!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>گزارش بروزرسانی | SmartPanelBot</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: Tahoma, 'Segoe UI', sans-serif;
      background: #f0f2f5;
      color: #222;
      padding: 24px 16px 60px;
    }
    .wrap { max-width: 1100px; margin: 0 auto; }
    /* جدول محصولات از یک ظرف عریض‌تر و مستقل استفاده می‌کند تا روی دسکتاپ ستون‌های بیشتری راحت جا شوند */
    .wrap-wide { max-width: 1560px; margin: 0 auto; }

    /* Header */
    .header {
      background: linear-gradient(135deg, #1a73e8, #0d47a1);
      color: #fff;
      border-radius: 14px;
      padding: 28px 32px;
      margin-bottom: 20px;
      box-shadow: 0 4px 18px rgba(26,115,232,.35);
    }
    .header h1 { font-size: 22px; margin-bottom: 6px; }
    .header .meta { font-size: 13px; opacity: .85; line-height: 1.9; }
    .header .meta span { margin-left: 20px; }

    /* Stats */
    .stats { display: flex; gap: 14px; margin-bottom: 20px; flex-wrap: wrap; }
    .stat {
      flex: 1 1 130px;
      border-radius: 12px;
      padding: 18px 14px;
      text-align: center;
      color: #fff;
      box-shadow: 0 2px 10px rgba(0,0,0,.12);
    }
    .stat.green { background: linear-gradient(135deg,#27ae60,#1e8449); }
    .stat.red   { background: linear-gradient(135deg,#e74c3c,#c0392b); }
    .stat.blue  { background: linear-gradient(135deg,#2980b9,#1a5276); }
    .stat-num  { font-size: 34px; font-weight: 700; }
    .stat-lbl  { font-size: 12px; margin-top: 4px; opacity: .9; }

    /* Card */
    .card {
      background: #fff;
      border-radius: 12px;
      padding: 22px 24px;
      margin-bottom: 18px;
      box-shadow: 0 2px 10px rgba(0,0,0,.07);
    }
    .card.danger { border-right: 4px solid #e74c3c; }
    .card h2 {
      font-size: 16px;
      margin-bottom: 14px;
      color: #333;
      border-bottom: 1px solid #eee;
      padding-bottom: 10px;
    }
    .note {
      font-size: 13px; color: #777;
      margin-bottom: 12px; padding: 8px 12px;
      background: #fff8e1; border-radius: 6px;
      border-right: 3px solid #f39c12;
    }

    /* Base table */
    .data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }
    .data-table th, .data-table td {
      padding: 10px 12px;
      text-align: right;
      border-bottom: 1px solid #f0f0f0;
      white-space: nowrap;
    }
    .data-table thead th {
      background: #f7f8fa;
      font-weight: 600;
      color: #555;
      position: sticky;
      top: 0;
      z-index: 1;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td { background: #f5f8ff; }

    /* Badge */
    .badge {
      display: inline-block; padding: 3px 10px;
      border-radius: 20px; font-size: 12px; font-weight: 600;
    }
    .badge.on   { background: #e8f5e9; color: #27ae60; }
    .badge.off  { background: #fce4e4; color: #e74c3c; }
    .badge.info { background: #e3f2fd; color: #1565c0; }

    /* Products card overrides */
    #products-card { padding-bottom: 16px; }
    .products-header {
      display: flex;
      align-items: center;
      gap: 14px;
      flex-wrap: wrap;
      margin-bottom: 14px;
      border-bottom: 1px solid #eee;
      padding-bottom: 12px;
    }
    .products-header h2 {
      margin: 0; border: none; padding: 0; flex: 1; min-width: 160px;
    }
    .total-badge {
      display: inline-block;
      background: #e3f2fd; color: #1565c0;
      font-size: 13px; font-weight: 700;
      padding: 2px 10px; border-radius: 20px;
      margin-right: 6px;
    }
    #search-box {
      flex: 0 0 220px;
      padding: 8px 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 13px;
      font-family: Tahoma, sans-serif;
      outline: none;
      direction: rtl;
    }
    #search-box:focus { border-color: #1a73e8; box-shadow: 0 0 0 2px rgba(26,115,232,.15); }

    .table-wrap {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      border-radius: 8px;
      border: 1px solid #f0f0f0;
    }
    /* اولویت این قانون باید از ".data-table td { white-space: nowrap }" بالاتر باشد، وگرنه اسم بلند از کادر بیرون می‌زند */
    .data-table th.col-name, .data-table td.col-name {
      max-width: 240px; min-width: 120px;
      white-space: normal; word-break: break-word; overflow-wrap: break-word;
      font-size: 12.5px; line-height: 1.5; color: #333;
    }
    .col-service { min-width: 60px; color: #666; font-size: 13px; }
    .col-num { width: 40px; text-align: center; }
    .col-unit { color: #1565c0; font-weight: 600; }
    .row-num { text-align: center; color: #aaa; font-size: 12px; }
    .price-changed { color: #27ae60; font-weight: 700; }

    /* تغییر اندازه دستی ستون‌های جدول گزارش */
    #products-table th { position: relative; }
    #products-table .col-resizer {
      position: absolute;
      top: 0; bottom: 0; left: -4px;
      width: 8px;
      cursor: col-resize;
      touch-action: none;
      z-index: 2;
    }
    #products-table .col-resizer:hover,
    body.col-resizing .col-resizer {
      background: rgba(26,115,232,.35);
    }
    body.col-resizing { cursor: col-resize; }
    body.col-resizing, body.col-resizing * { user-select: none; }

    .no-results {
      text-align: center; color: #999;
      padding: 32px; font-size: 14px;
    }

    /* Pagination */
    .pagination-bar {
      display: flex; align-items: center; justify-content: center;
      gap: 12px; margin-top: 14px; flex-wrap: wrap;
    }
    .pg-btn {
      background: #1a73e8; color: #fff;
      border: none; border-radius: 8px;
      padding: 7px 18px; font-size: 13px;
      cursor: pointer; font-family: Tahoma, sans-serif;
      transition: background .15s;
    }
    .pg-btn:hover:not(:disabled) { background: #1558b0; }
    .pg-btn:disabled { background: #ccc; cursor: default; }
    #pg-info { font-size: 13px; color: #555; min-width: 100px; text-align: center; }

    /* Footer */
    .footer {
      text-align: center; font-size: 12px; color: #aaa; margin-top: 24px;
    }

    /* Responsive tweaks */
    @media (max-width: 600px) {
      .header { padding: 18px; }
      .header h1 { font-size: 18px; }
      .stat-num { font-size: 28px; }
      .card { padding: 16px; }
      #search-box { flex: 1 1 100%; }
      .data-table th, .data-table td { padding: 8px 10px; font-size: 13px; }
    }
  </style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <h1><?= $icon ?> <?= $type_label ?></h1>
    <div class="meta">
      <span>📡 وب‌سرویس: <strong><?= $api ?></strong></span>
      <span>🕐 <?= $date_str ?></span>
    </div>
  </div>

  <?= $stats ?>

  <?= $category_html ?>

  <?= $settings_html ?>

  <?= $disabled_html ?>

</div>

<?php if ($products_html): ?>
<div class="wrap-wide">
  <?= $products_html ?>
</div>
<?php endif; ?>

<div class="wrap">
  <div class="footer">SmartPanelBot — گزارش اختصاصی بروزرسانی</div>
</div>

<?php if (!empty($products_list)): ?>
<script>
const PER_PAGE = 25;
let currentPage = 1;
let visibleRows = [];

function getAllRows() {
  return Array.from(document.querySelectorAll('#table-body tr'));
}

function filterTable(q) {
  q = (q || '').trim().toLowerCase();
  const rows = getAllRows();
  visibleRows = rows.filter(r => {
    if (!q) return true;
    const name = r.querySelector('.col-name')?.textContent?.toLowerCase() || '';
    const service = r.querySelector('.col-service')?.textContent?.toLowerCase() || '';
    return name.includes(q) || service.includes(q);
  });
  rows.forEach(r => r.style.display = 'none');
  currentPage = 1;
  renderPage();
}

function renderPage() {
  const total = visibleRows.length;
  const totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
  if (currentPage > totalPages) currentPage = totalPages;

  const start = (currentPage - 1) * PER_PAGE;
  const end   = start + PER_PAGE;

  visibleRows.forEach((r, i) => {
    r.style.display = (i >= start && i < end) ? '' : 'none';
  });

  document.getElementById('pg-info').textContent =
    total > 0 ? ('صفحه ' + currentPage + ' از ' + totalPages + ' — ' + total + ' محصول') : '';
  document.getElementById('no-results').style.display = total === 0 ? '' : 'none';
  document.getElementById('pg-prev').disabled = currentPage <= 1;
  document.getElementById('pg-next').disabled = currentPage >= totalPages;
  document.getElementById('pagination-bar').style.display = totalPages <= 1 ? 'none' : 'flex';
}

function changePage(dir) {
  currentPage += dir;
  renderPage();
}

// init
visibleRows = getAllRows();
renderPage();

(function initResizableColumns() {
  var table = document.getElementById('products-table');
  if (!table) return;
  var ths = table.querySelectorAll('thead th');
  if (!ths.length) return;

  ths.forEach(function (th) {
    th.style.width = th.offsetWidth + 'px';
  });
  table.style.tableLayout = 'fixed';
  table.style.width = 'auto';

  var MIN_W = 36;

  ths.forEach(function (th) {
    var grip = document.createElement('span');
    grip.className = 'col-resizer';
    th.appendChild(grip);

    var startX = 0;
    var startWidth = 0;
    var active = false;

    grip.addEventListener('pointerdown', function (e) {
      active = true;
      startX = e.clientX;
      startWidth = th.offsetWidth;
      grip.setPointerCapture(e.pointerId);
      document.body.classList.add('col-resizing');
      e.preventDefault();
    });
    grip.addEventListener('pointermove', function (e) {
      if (!active) return;
      var delta = e.clientX - startX;
      var newWidth = Math.max(MIN_W, startWidth - delta);
      th.style.width = newWidth + 'px';
    });
    grip.addEventListener('pointerup', function (e) {
      if (!active) return;
      active = false;
      grip.releasePointerCapture(e.pointerId);
      document.body.classList.remove('col-resizing');
    });
  });
})();
</script>
<?php endif; ?>
</body>
</html>
