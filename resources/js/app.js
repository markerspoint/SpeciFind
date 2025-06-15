import './bootstrap';

function debounce(func, delay) {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), delay);
  };
}

const input = document.getElementById('organismInput');
const suggestionsBox = document.getElementById('suggestions');

const resultBox = document.getElementById('result');
const errorBox = document.getElementById('error');
const loadingText = document.getElementById('loading');

const nameEl = document.getElementById('result-name');
const familyEl = document.getElementById('result-family');
const descriptionEl = document.getElementById('result-description');

// Autocomplete suggestion logic
input.addEventListener('input', debounce(async () => {
  const query = input.value.trim();

  if (!query) {
    suggestionsBox.innerHTML = '';
    suggestionsBox.classList.add('hidden');
    return;
  }

  try {
    const response = await fetch(`/autocomplete-suggestions?query=${encodeURIComponent(query)}`);
    const suggestions = await response.json();

    if (suggestions.length === 0) {
      suggestionsBox.innerHTML = '<li class="p-2 text-gray-500">No suggestions</li>';
    } else {
      suggestionsBox.innerHTML = suggestions.map(s =>
        `<li class="p-2 hover:bg-green-100 cursor-pointer">${s}</li>`
      ).join('');
    }

    suggestionsBox.classList.remove('hidden');

    Array.from(suggestionsBox.children).forEach(item => {
      item.addEventListener('click', () => {
        input.value = item.textContent;
        suggestionsBox.classList.add('hidden');
      });
    });
  } catch (err) {
    suggestionsBox.innerHTML = '<li class="p-2 text-red-500">Error loading suggestions</li>';
    suggestionsBox.classList.remove('hidden');
  }
}, 250));

document.addEventListener('click', e => {
  if (!input.contains(e.target) && !suggestionsBox.contains(e.target)) {
    suggestionsBox.classList.add('hidden');
  }
});

// Main form submission logic
document.getElementById('findForm').addEventListener('submit', async e => {
  e.preventDefault();
  suggestionsBox.classList.add('hidden');

  const organism = input.value.trim();

  // Show loading
  resultBox.classList.add('hidden');
  errorBox.classList.add('hidden');
  loadingText.textContent = `Searching for scientific name of "${organism}"...`;
  loadingText.classList.remove('hidden');

  try {
    const response = await fetch('/find-scientific-name', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ organism })
    });

    const data = await response.json();

    loadingText.classList.add('hidden');

    if (data.success) {
      const cleaned = data.result
        .trim()
        .replace(/^```(?:json)?\s*/i, '')
        .replace(/```$/, '')
        .trim();

      const parsed = JSON.parse(cleaned);

      nameEl.textContent = parsed.scientificName || 'Not found';
      familyEl.textContent = parsed.family || 'Not found';
      descriptionEl.textContent = parsed.description || 'No description available.';

      resultBox.classList.remove('hidden');
    } else {
      errorBox.textContent = data.message || 'Unknown error occurred.';
      errorBox.classList.remove('hidden');
    }
  } catch (err) {
    loadingText.classList.add('hidden');
    errorBox.textContent = 'Error connecting to server.';
    errorBox.classList.remove('hidden');
  }
});

// Blur delay to allow suggestion click
input.addEventListener('blur', () => {
  setTimeout(() => {
    suggestionsBox.classList.add('hidden');
  }, 150);
});
