<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Search Page</title>
    <style>
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

        .result-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .result-item h2 {
            font-size: 1.2rem;
            color: #4285f4;
            margin: 0 0 10px;
        }

        .result-item p {
            font-size: 1rem;
            color: #555;
            margin: 0 0 10px;
        }

        .result-item img, .result-item video {
            margin-top: 10px;
            width: 100%;
            max-width: 100%;
            border-radius: 8px;
        }

        /* Responsive adjustments */
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
                    throw new Error('No matching results found.');
                }
                return response.json();
            })
            .then(data => {
                const resultsDiv = document.getElementById('results');
                resultsDiv.innerHTML = '';

                data.forEach(item => {
                    const itemDiv = document.createElement('div');
                    itemDiv.classList.add('result-item');
                    itemDiv.innerHTML = `
                        <h2>${item.title}</h2>
                        <p>${item.description}</p>
                        <img src="/storage/${item.foto}" alt="${item.title}">
                        <video controls>
                            <source src="/storage/${item.video}" type="video/mp4">
                            Your browser does not support the video tag.
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
