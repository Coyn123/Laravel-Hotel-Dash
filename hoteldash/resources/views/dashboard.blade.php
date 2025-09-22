<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Hospitality Dashboard</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<header>
  <div class="header-inner">
    <div class="brand">
      <div class="logo" aria-hidden="true">Coyner</div>
      <div class="title">Hospitality Dashboard</div>
    </div>
    <div class="controls" style="margin-left:auto">
      <div class="notification-wrapper">
        <button class="notification-btn"
                id="notificationBtn"
                aria-label="View notifications"
                aria-expanded="false"
                aria-controls="notificationBox">
        </button>
        <div class="notification-box" id="notificationBox">
          <h4 class="notification-title">Notifications</h4>
          <div class="notification-content" id="notificationContent">
            <p>Loading…</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<main>
  <section class="panel" aria-labelledby="rooms-title">
    <div class="panel-header">
      <h2 id="rooms-title" class="panel-title">Rooms</h2>
    </div>
    <div class="panel-body">

      @foreach($floors as $floor)
        <details class="floor" id="floor-{{ $floor['id'] }}" {{ $loop->first ? 'open' : '' }}>
          <summary class="floor-header">
            <div class="floor-meta">
              <span class="floor-name">{{ $floor['name'] }}</span>
              <span class="floor-sub">{{ ($floor['end'] - $floor['start']) }} rooms</span>
            </div>
            <svg class="chevron" viewBox="0 0 20 20" fill="currentColor">
              <path d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"/>
            </svg>
          </summary>
          <div class="rooms">
            @for($room = $floor['start']; $room < $floor['end']; $room++)
              <a class="room" href="#">{{ $room }}</a>
            @endfor
          </div>
        </details>
      @endforeach

    </div>
  </section>
</main>
