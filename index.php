<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Stream Hub</title>
  <link rel="stylesheet" href="public/style.css" />
</head>
<body>
  <header>
    <h1>Stream Hub</h1>
    <div id="profile" class="hidden">
      <img id="profile-photo" alt="Profile photo" />
      <span id="profile-name"></span>
      <button id="logout-btn">Log out</button>
    </div>
  </header>

  <main>
    <section id="auth-section">
      <div class="auth-card">
        <h2>Create Account</h2>
        <input id="signup-name" type="text" placeholder="Full name" />
        <input id="signup-email" type="email" placeholder="Email" />
        <input id="signup-password" type="password" placeholder="Password" />
        <input id="signup-photo" type="file" accept="image/*" />
        <button id="signup-btn">Create account</button>
      </div>

      <div class="auth-card">
        <h2>Sign In</h2>
        <input id="signin-email" type="email" placeholder="Email" />
        <input id="signin-password" type="password" placeholder="Password" />
        <button id="signin-btn">Sign in</button>
      </div>
    </section>

    <section id="catalog-section" class="hidden"></section>
  </main>

  <div id="player-modal" class="modal hidden">
    <div class="modal-content">
      <button id="close-modal">✕</button>
      <iframe id="player-frame" title="YouTube player" allowfullscreen></iframe>
    </div>
  </div>

  <script src="public/app.js"></script>
</body>
</html>
