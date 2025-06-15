<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <title>SpeciFind</title>

    <!-- Google font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="relative bg-gradient-to-b from-green-100 via-green-200 via-blue-100 to-white flex items-center justify-center min-h-screen font-poppins overflow-hidden">

    <!-- Floating species SVGs -->

    <svg class="floating-species species-1" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true">
        <path fill="#22c55e" d="M32 2C21 8 15 21 15 32c0 11 7 22 17 22s17-10 17-22c0-11-7-24-17-30z" />
    </svg>

    <svg class="floating-species species-2" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true">
        <circle cx="32" cy="32" r="30" stroke="#16a34a" stroke-width="3" />
        <path fill="#22c55e" d="M32 10l12 24-12 24-12-24 12-24z" />
    </svg>

    <svg class="floating-species species-3" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true">
        <ellipse cx="32" cy="32" rx="22" ry="30" fill="#4ade80" />
        <circle cx="32" cy="32" r="10" fill="#22c55e" />
    </svg>

    <svg class="floating-species species-4" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true">
        <path d="M20 44c10-10 24-24 24-24" stroke="#22c55e" stroke-width="4" stroke-linecap="round" />
        <circle cx="20" cy="44" r="4" fill="#16a34a" />
    </svg>


    <svg class="floating-species species-5" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true">
        <path fill="#34d399" d="M32 12c-5 5-7 15-7 20s4 12 7 12 7-7 7-12-2-15-7-20z" />
    </svg>

    <svg class="floating-species species-6" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true">
        <circle cx="32" cy="32" r="20" fill="#22c55e" />
        <circle cx="32" cy="32" r="8" fill="#166534" />
    </svg>

    <svg class="floating-species species-7" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true">
        <path stroke="#16a34a" stroke-width="3" d="M20 20l24 24M44 20L20 44" />
    </svg>


    {{-- parent div --}}
    <div
        class="bg-gradient-to-br from-white/80 via-green-50 to-green-100 backdrop-blur-md p-10 rounded-2xl shadow-2xl sm:w-[30rem] md:w-[35rem] lg:w-[40rem] border border-green-200 max-w-full mx-4 relative z-10 card-glow">

        <header class="mb-8 text-center">
            <h1
                class="text-3xl font-[900] text-green-700 mb-2 flex items-center justify-center gap-3 select-none cursor-default">
                <img src="{{ asset('images/logo.png') }}" alt="SpeciFind Logo" class="w-10 h-10 object-contain" />
                SpeciFind
            </h1>
            <p class="text-gray-600 font-medium max-w-md mx-auto">
                Discover scientific names of plants, animals &amp; more
            </p>
        </header>

        <form id="findForm" class="space-y-6" autocomplete="off" novalidate>
            <div class="relative w-full">
                <input type="text" id="organismInput" name="organism"
                    placeholder="Enter common name (e.g. rose, lion)" required
                    class="w-full px-5 py-3 border border-green-300 rounded-t-lg rounded-b-none focus:outline-none focus:ring-4 focus:ring-green-300 focus:border-green-400 transition shadow-sm" />

                <ul id="suggestions"
                    class="absolute z-30 w-full bg-white border border-green-300 rounded-b-lg max-h-44 overflow-y-auto hidden text-left shadow-lg"
                    role="listbox" tabindex="-1">
                </ul>
            </div>

            <button type="submit"
                class="w-full bg-green-600 text-white py-3 rounded-b-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-400 font-semibold shadow-md transition duration-300 ease-in-out">
                Find Scientific Name
            </button>
        </form>

        <div id="result" class="mt-8 text-gray-700 text-left w-full break-words"></div>
    </div>

    <footer class="absolute bottom-[2rem] left-0 w-full text-center text-sm text-gray-600 z-0 select-none">
        Developed by
        <a href="https://www.facebook.com/mrkdlcrz.ide" target="_blank" rel="noopener noreferrer"
            class="font-semibold text-green-700 hover:underline hover:text-green-800 transition">
            Mark Ian Dela Cruz
        </a>
    </footer>


    <script>
        document.getElementById('findForm').addEventListener('submit', async e => {
            e.preventDefault();
            const organism = document.getElementById('organismInput').value.trim();
            const resultDiv = document.getElementById('result');

            resultDiv.innerHTML =
                `<p class="text-gray-600 text-center">Searching for scientific name of <strong>${organism}</strong>...</p>`;

            try {
                const response = await fetch('/find-scientific-name', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        organism
                    })
                });

                const data = await response.json();

                if (data.success) {
                    try {
                        const cleaned = data.result
                            .trim()
                            .replace(/^```(?:json)?\s*/i, '')
                        .replace(/```$/, '')
                            .trim();

                        const parsed = JSON.parse(cleaned);

                        resultDiv.innerHTML = `
                <div class="bg-green-100 text-green-800 p-4 rounded mt-4 text-left space-y-2 w-full break-words">
                    <p><strong>Scientific Name:</strong> ${parsed.scientificName || 'Not found'}</p>
                    <p><strong>Family:</strong> ${parsed.family || 'Not found'}</p>
                    <p><strong>Description:</strong> ${parsed.description || 'No description available.'}</p>
                </div>
            `;
                    } catch (err) {
                        resultDiv.innerHTML = `
                <div class="bg-yellow-100 text-yellow-800 p-4 rounded mt-4 w-full mx-auto break-words">
                    <p><strong>Note:</strong> Couldn't parse JSON. Showing raw output:</p>
                    <pre class="whitespace-pre-wrap text-left">${data.result}</pre>
                </div>
            `;
                    }
                } else {
                    resultDiv.innerHTML = `
                <div class="bg-red-100 text-red-700 p-4 rounded-lg shadow-inner w-full mx-auto break-words">
                    <p><strong>Error:</strong> ${data.message}</p>
                </div>
            `;
                }
            } catch (err) {
                resultDiv.innerHTML = `
                <div class="!bg-red-100 text-red-700 p-4 rounded-lg shadow-inner w-full mx-auto break-words">
                    <p><strong>Error connecting to server.</strong></p>
                </div>
            `;
            }
        });
    </script>

</body>

</html>
