<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post | Blogger Clone</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        :root {
            --primary: #ff4b2b;
            --secondary: #ff416c;
            --dark: #1a1a1a;
            --light: #f8f9fa;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f0f2f5;
            padding: 2rem;
        }

        .header-nav {
            max-width: 800px;
            margin: 0 auto 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-link {
            text-decoration: none;
            color: var(--dark);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: var(--primary);
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            margin-bottom: 2rem;
            color: var(--dark);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
        }

        input[type="text"], select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid var(--border);
            border-radius: 12px;
            outline: none;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus, select:focus {
            border-color: var(--primary);
        }

        #editor-container {
            height: 400px;
            border-radius: 0 0 12px 12px;
            border: 2px solid var(--border);
        }

        .ql-toolbar {
            border-radius: 12px 12px 0 0;
            border: 2px solid var(--border) !important;
            border-bottom: none !important;
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            width: 100%;
            margin-top: 2rem;
            transition: transform 0.3s;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 65, 108, 0.3);
        }

        @media (max-width: 768px) {
            .container { padding: 1.5rem; }
            body { padding: 1rem; }
        }
    </style>
</head>
<body>

    <div class="header-nav">
        <a href="javascript:void(0)" onclick="navigate('index.php')" class="back-link">← Back to Feed</a>
    </div>

    <main class="container">
        <h1>Create a New Story</h1>
        <form id="post-form">
            <div class="form-group">
                <label for="title">Post Title</label>
                <input type="text" id="title" name="title" placeholder="Epic title goes here..." required>
            </div>

            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="author">Author Name</label>
                    <input type="text" id="author" name="author" placeholder="Your Name" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="Technology">Technology</option>
                        <option value="Lifestyle">Lifestyle</option>
                        <option value="Business">Business</option>
                        <option value="Travel">Travel</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Content</label>
                <div id="editor-container"></div>
                <input type="hidden" name="content" id="content">
            </div>

            <button type="submit" class="submit-btn" id="submit-btn">Publish Story</button>
        </form>
    </main>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Tell your story...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'image'],
                    ['clean']
                ]
            }
        });

        function navigate(url) {
            window.location.href = url;
        }

        document.getElementById('post-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('submit-btn');
            btn.innerText = 'Publishing...';
            btn.disabled = true;

            const content = quill.root.innerHTML;
            document.getElementById('content').value = content;

            const formData = new FormData(this);
            formData.append('action', 'create');

            try {
                const response = await fetch('process_post.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if(result.status === 'success') {
                    // Use JS redirection as requested
                    navigate('index.php');
                } else {
                    alert('Error: ' + result.message);
                    btn.innerText = 'Publish Story';
                    btn.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
                btn.innerText = 'Publish Story';
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>
