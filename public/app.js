const authSection = document.getElementById('auth-section');
const catalogSection = document.getElementById('catalog-section');
const profile = document.getElementById('profile');
const profileName = document.getElementById('profile-name');
const profilePhoto = document.getElementById('profile-photo');
const playerModal = document.getElementById('player-modal');
const playerFrame = document.getElementById('player-frame');

const state = { catalog: null };

init();

document.getElementById('signup-btn').addEventListener('click', async () => {
  const name = document.getElementById('signup-name').value.trim();
  const email = document.getElementById('signup-email').value.trim();
  const password = document.getElementById('signup-password').value;
  const photoFile = document.getElementById('signup-photo').files[0];
  const profilePhoto = photoFile ? await fileToBase64(photoFile) : '';

  const result = await api('signup', { name, email, password, profilePhoto });
  if (!result.ok) return;

  downloadCatalog(result.catalog, name);
  applySession(result.user, result.catalog);
});

document.getElementById('signin-btn').addEventListener('click', async () => {
  const email = document.getElementById('signin-email').value.trim();
  const password = document.getElementById('signin-password').value;
  const result = await api('signin', { email, password });
  if (result.ok) applySession(result.user, result.catalog);
});

document.getElementById('logout-btn').addEventListener('click', async () => {
  await fetch('api.php?action=logout');
  location.reload();
});

document.getElementById('close-modal').addEventListener('click', closePlayer);

async function init() {
  const res = await fetch('api.php?action=me');
  if (res.status === 200) {
    const data = await res.json();
    applySession(data.user, data.catalog);
  }
}

function applySession(user, catalog) {
  state.catalog = catalog;
  authSection.classList.add('hidden');
  catalogSection.classList.remove('hidden');
  profile.classList.remove('hidden');

  profileName.textContent = user.name;
  profilePhoto.src = user.profilePhoto || 'https://placehold.co/80x80/png';

  renderCatalog(catalog);
}

function renderCatalog(catalog) {
  catalogSection.innerHTML = '';
  Object.entries(catalog).forEach(([category, videos]) => {
    const section = document.createElement('section');
    section.className = 'category';
    section.innerHTML = `<h2>${capitalize(category)} (100 videos)</h2>`;

    const grid = document.createElement('div');
    grid.className = 'grid';

    videos.forEach((video) => {
      const card = document.createElement('article');
      card.className = 'video-card';
      card.innerHTML = `
        <h4>${video.title}</h4>
        <div class="actions">
          <button data-play="${video.videoId}">Play</button>
          <a href="https://www.youtube.com/watch?v=${video.videoId}" target="_blank" rel="noopener">
            <button>Open on YouTube</button>
          </a>
        </div>
      `;
      card.querySelector('[data-play]').addEventListener('click', () => openPlayer(video.videoId));
      grid.appendChild(card);
    });

    section.appendChild(grid);
    catalogSection.appendChild(section);
  });
}

function openPlayer(videoId) {
  playerFrame.src = `https://www.youtube.com/embed/${videoId}`;
  playerModal.classList.remove('hidden');
}

function closePlayer() {
  playerModal.classList.add('hidden');
  playerFrame.src = '';
}

function downloadCatalog(catalog, username) {
  const blob = new Blob([JSON.stringify(catalog, null, 2)], { type: 'application/json' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = `${username.toLowerCase().replace(/\s+/g, '-')}-video-library.json`;
  link.click();
  URL.revokeObjectURL(link.href);
}

async function api(action, payload) {
  const res = await fetch(`api.php?action=${action}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  });

  const data = await res.json();
  if (!res.ok) {
    alert(data.error || 'Request failed');
    return { ok: false };
  }
  return data;
}

function fileToBase64(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.onerror = reject;
    reader.readAsDataURL(file);
  });
}

function capitalize(value) {
  return value.charAt(0).toUpperCase() + value.slice(1);
}
