<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Halaman Pencarian</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .results { margin-top: 20px; }
        .result-item { margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Halaman Pencarian</h1>
    <form id="searchForm">
        <input type="text" id="keyword" placeholder="Masukkan kata kunci" required>
        <button type="submit">Cari</button>
    </form>
    <div class="results" id="results"></div>

    <script>
        document.getElementById('searchForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const keyword = document.getElementById('keyword').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ keyword: keyword })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Tidak ada hasil yang cocok.');
                }
                return response.json();
            })
            .then(data => {
                const resultsDiv = document.getElementById('results');
                resultsDiv.innerHTML = ''; // Bersihkan hasil sebelumnya

                data.forEach(item => {
                    const itemDiv = document.createElement('div');
                    itemDiv.classList.add('result-item');
                    itemDiv.innerHTML = `
                        <h2>${item.title}</h2>
                        <p>${item.description}</p>
                        <img src="/storage/${item.foto}" alt="${item.title}" width="200">
                        <video controls width="200">
                            <source src="/storage/${item.video}" type="video/mp4">
                            Browser Anda tidak mendukung video.
                        </video>
                    `;
                    resultsDiv.appendChild(itemDiv);
                });
            })
            .catch(error => {
                const resultsDiv = document.getElementById('results');
                resultsDiv.innerHTML = `<p>${error.message}</p>`;
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
