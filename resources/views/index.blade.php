<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Search Page</title>
    <style>
        /* Styling tetap sama seperti sebelumnya */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            min-height: 100vh;
            box-sizing: border-box;
        }

        h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
        }

        #searchForm {
            display: flex;
            width: 100%;
            max-width: 600px;
            gap: 5px;
            position: relative;
        }

        #keyword {
            flex: 1;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            outline: none;
        }

        #keyword:focus {
            border-color: #4285f4;
        }

        button[type="submit"] {
            padding: 10px;
            font-size: 1rem;
            color: #fff;
            background-color: #4285f4;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            white-space: nowrap;
        }

        button[type="submit"]:hover {
            background-color: #357ae8;
        }

        #suggestions {
            position: absolute;
            top: 40px;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 10;
            display: none;
        }

        .suggestion-item {
            padding: 10px;
            cursor: pointer;
        }

        .suggestion-item:hover {
            background-color: #f0f0f0;
        }

        .results {
            margin-top: 20px;
            width: 100%;
            max-width: 800px;
        }

        .result-item {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            overflow: hidden;
            box-sizing: border-box;
        }

        .result-item h2 {
            font-size: 1.2rem;
            color: #4285f4;
            margin: 0 0 10px;
            cursor: pointer;
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 1.5rem;
            }

            #searchForm {
                flex-direction: column;
                gap: 10px;
            }

            button[type="submit"] {
                font-size: 1rem;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <h1>SARASWATI</h1>
    <form id="searchForm">
        <input type="text" id="keyword" placeholder="Enter a keyword" required>
        <button type="submit">Search</button>
        <div id="suggestions"></div>
    </form>
    <div class="results" id="results"></div>

    <script>
        const searchForm = document.getElementById('searchForm');
        const keywordInput = document.getElementById('keyword');
        const suggestionsDiv = document.getElementById('suggestions');
        const resultsDiv = document.getElementById('results');
        let debounceTimeout;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Fungsi untuk membuat item saran
        const createSuggestionItem = (text) => {
            const itemDiv = document.createElement('div');
            itemDiv.classList.add('suggestion-item');
            itemDiv.textContent = text;
            itemDiv.onclick = () => {
                keywordInput.value = text;
                suggestionsDiv.innerHTML = ''; // Clear suggestions
                fetchSentences(text); // Ambil hasil pencarian saat saran diklik
                suggestionsDiv.style.display = 'none'; // Sembunyikan saran setelah klik
            };
            return itemDiv;
        };

        // Fungsi untuk membuat item hasil pencarian
        const createResultItem = (item) => {
            const itemDiv = document.createElement('div');
            itemDiv.classList.add('result-item');

            // Menampilkan judul dengan tautan ke halaman detail
            itemDiv.innerHTML = `
                <h2>${item.title}</h2>
            `;

            const elemen = itemDiv.addEventListener('click', (e) => {
                itemDiv.innerHTML =
                    `<h2>${item.title}</h2>
                <p>${item.description}</p>
                <img src="/storage/${item.foto}" alt="${item.title}" width="200" height="200">
                <video controls width="200" height="200">
                <source src="/storage/${item.video}" type="video/mp4">
                Browser Anda tidak mendukung video.
                </video>`;
            })
            return itemDiv;
        };

        // Fungsi untuk mengambil saran
        const fetchSuggestions = (keyword) => {
            fetch('/suggestions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        keyword
                    })
                })
                .then(response => response.json())
                .then(data => {
                    suggestionsDiv.innerHTML = ''; // Clear previous suggestions
                    if (data && data.length > 0) {
                        data.forEach(item => {
                            suggestionsDiv.appendChild(createSuggestionItem(item.title));
                        });
                        suggestionsDiv.style.display = 'block'; // Menampilkan saran
                    } else {
                        suggestionsDiv.style.display = 'none'; // Menyembunyikan saran jika tidak ada
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    suggestionsDiv.innerHTML = '<p>Error fetching suggestions.</p>';
                    suggestionsDiv.style.display = 'none'; // Hide on error
                });
        };

        // Fungsi untuk mengambil hasil pencarian
        const fetchSentences = (keyword) => {
            fetch('/search', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        keyword
                    })
                })
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML = ''; // Clear previous results
                    if (data && data.length > 0) {
                        data.forEach(item => {
                            resultsDiv.appendChild(createResultItem(item));
                        });
                    } else {
                        resultsDiv.innerHTML = '<p>Tidak ada hasil yang ditemukan.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultsDiv.innerHTML = '<p>Error fetching results.</p>';
                });
        };

        // Event listener untuk input keyword dengan debounce
        keywordInput.addEventListener('input', function(event) {
            const keyword = keywordInput.value.trim();

            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                if (keyword) {
                    fetchSuggestions(keyword); // Ambil saran saat input lebih dari 2 karakter
                } else {
                    suggestionsDiv.innerHTML = ''; // Clear suggestions
                    suggestionsDiv.style.display = 'none'; // Hide suggestions if input is too short
                }
            }, 300); // Debounce time of 300ms
        });

        // Event listener untuk submit form pencarian
        searchForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Menghentikan submit form

            const keyword = keywordInput.value.trim();
            if (keyword) {
                resultsDiv.innerHTML = ''; // Clear previous results
                fetchSentences(keyword); // Ambil hasil pencarian langsung setelah submit
            }

            // Clear suggestions saat form disubmit
            suggestionsDiv.innerHTML = ''; // Clear suggestions after submit
            suggestionsDiv.style.display = 'none'; // Hide suggestions after form submit
        });
    </script>
</body>

</html>
