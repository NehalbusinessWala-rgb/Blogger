<?php
require_once 'config.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$query = "SELECT * FROM posts WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (title LIKE ? OR content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogger Clone | Explore Amazing Stories</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ff4b2b;
            --secondary: #ff416c;
            --dark: #1a1a1a;
            --light: #f8f9fa;
            --gray: #6c757d;
            --glass: rgba(255, 255, 255, 0.9);
            --shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f0f2f5;
            color: var(--dark);
            line-height: 1.6;
        }

        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 4rem 2rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        header::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: 0;
            width: 100%;
            height: 100px;
            background: #f0f2f5;
            transform: skewY(-2deg);
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -1px;
        }

        .search-container {
            max-width: 600px;
            margin: 2rem auto 0;
            position: relative;
            z-index: 10;
        }

        .search-bar {
            width: 100%;
            padding: 1.2rem 2rem;
            border-radius: 50px;
            border: none;
            box-shadow: var(--shadow);
            font-size: 1.1rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-bar:focus {
            transform: scale(1.02);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .nav-categories {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 3rem auto;
            flex-wrap: wrap;
            padding: 0 1rem;
        }

        .category-btn {
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            background: white;
            color: var(--dark);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 2px solid transparent;
        }

        .category-btn:hover, .category-btn.active {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(255, 75, 43, 0.3);
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem;
        }

        .create-btn-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 100;
        }

        .btn-create {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            box-shadow: 0 10px 20px rgba(255, 65, 108, 0.4);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-create:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 30px rgba(255, 65, 108, 0.5);
        }

        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2.5rem;
        }

        .blog-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .blog-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }

        .card-content {
            padding: 2rem;
            flex-grow: 1;
        }

        .card-category {
            display: inline-block;
            padding: 0.3rem 1rem;
            background: rgba(255, 75, 43, 0.1);
            color: var(--primary);
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-transform: uppercase;
        }

        .card-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.3;
            color: var(--dark);
            text-decoration: none;
            display: block;
        }

        .card-title:hover {
            color: var(--primary);
        }

        .card-excerpt {
            color: var(--gray);
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-footer {
            padding: 1.5rem 2rem;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
        }

        .author-info {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .author-avatar {
            width: 35px;
            height: 35px;
            background: #ddd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
        }

        .no-posts {
            grid-column: 1 / -1;
            text-align: center;
            padding: 5rem;
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow);
        }

        @media (max-width: 768px) {
            h1 { font-size: 2.5rem; }
            .container { padding: 1rem; }
            .blog-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <header>
        <h1>Blogger Clone</h1>
        <p>Share your stories with the world</p>
        <div class="search-container">
            <form id="search-form">
                <input type="text" name="search" class="search-bar" placeholder="Search for posts..." value="<?php echo htmlspecialchars($search); ?>">
            </form>
        </div>
    </header>

    <div class="nav-categories">
        <a href="javascript:void(0)" onclick="navigate('index.php')" class="category-btn <?php echo empty($category) ? 'active' : ''; ?>">All</a>
        <a href="javascript:void(0)" onclick="navigate('index.php?category=Technology')" class="category-btn <?php echo $category == 'Technology' ? 'active' : ''; ?>">Technology</a>
        <a href="javascript:void(0)" onclick="navigate('index.php?category=Lifestyle')" class="category-btn <?php echo $category == 'Lifestyle' ? 'active' : ''; ?>">Lifestyle</a>
        <a href="javascript:void(0)" onclick="navigate('index.php?category=Business')" class="category-btn <?php echo $category == 'Business' ? 'active' : ''; ?>">Business</a>
        <a href="javascript:void(0)" onclick="navigate('index.php?category=Travel')" class="category-btn <?php echo $category == 'Travel' ? 'active' : ''; ?>">Travel</a>
    </div>

    <main class="container">
        <div class="blog-grid">
            <?php if (empty($posts)): ?>
                <div class="no-posts">
                    <h2>No posts found.</h2>
                    <p>Be the first to share an amazing story!</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="blog-card">
                        <div class="card-content">
                            <span class="card-category"><?php echo htmlspecialchars($post['category']); ?></span>
                            <a href="javascript:void(0)" onclick="navigate('post.php?id=<?php echo $post['id']; ?>')" class="card-title">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                            <div class="card-excerpt">
                                <?php echo strip_tags($post['content']); ?>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="author-info">
                                <div class="author-avatar"><?php echo strtoupper(substr($post['author'], 0, 1)); ?></div>
                                <span><?php echo htmlspecialchars($post['author']); ?></span>
                            </div>
                            <span style="color: var(--gray);"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <div class="create-btn-container">
        <a href="javascript:void(0)" onclick="navigate('create.php')" class="btn-create">
            <span>+ Create New Post</span>
        </a>
    </div>

    <script>
        // Use JS for redirection as requested
        function navigate(url) {
            window.location.href = url;
        }

        document.getElementById('search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const searchVal = this.querySelector('input').value;
            navigate('index.php?search=' + encodeURIComponent(searchVal));
        });
    </script>
</body>
</html>
