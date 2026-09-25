<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Error')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --primary: #4f46e5;
            --bg: #f9fafb;
            --text: #1f2937;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: var(--bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .error-container {
            text-align: center;
            padding: 2rem;
        }

        .error-code {
            font-size: 6rem;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .error-message {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .error-description {
            color: #6b7280;
            margin-bottom: 2rem;
        }

        .home-link {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .home-link:hover {
            background: #4338ca;
        }

        @media (max-width: 600px) {
            .error-code {
                font-size: 4rem;
            }

            .error-message {
                font-size: 1.2rem;
            }
        }

        .error-page {
            text-align: center;
            padding: 100px 20px;
            background-color: #f8f9fa;
        }

        .error-code {
            font-size: 120px;
            font-weight: bold;
            color: #dc3545;
        }

        .error-message {
            font-size: 28px;
            font-weight: 600;
            color: #343a40;
        }

        .error-description {
            margin-top: 15px;
            font-size: 16px;
            color: #6c757d;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 6px;
            text-decoration: none;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="error-container">
        @yield('content')
    </div>
</body>

</html>
