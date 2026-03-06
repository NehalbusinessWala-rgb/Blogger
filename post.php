<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
a
// Fetch Post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

// Fetch Comments
$stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC");
$stmt->execute([$id]);
$comments = $stmt->fetchAll();

// Fetch Related Posts
$stmt = $pdo->prepare("SELECT id, title, category FROM posts WHERE category = ? AND id != ? LIMIT 3");
$stmt->execute([$post['category'], $id]);
$related = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> | Blogger Clone</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ff4b2b;
            --secondary: #ff416c;
            --dark: #1a1a1a;
            --gray: #6c757d;
            --light: #f8f9fa;
            --border: #eef2f7;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #fdfdfd; color: var(--dark); line-height: 1.8; }

        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 4px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            width: 0%;
            z-index: 1000;
        }

        nav {
            padding: 1.5rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-btn {
            text-decoration: none;
            color: var(--dark);
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .back-btn:hover { background: var(--light); color: var(--primary); }

        .edit-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .btn-edit { background: #e8f0fe; color: #1a73e8; }
        .btn-delete { background: #fce8e6; color: #d93025; border: none; cursor: pointer; }

        .btn-edit:hover { background: #d2e3fc; }
        .btn-delete:hover { background: #fad2cf; }

        article { max-width: 800px; margin: 4rem auto; padding: 0 2rem; }

        .post-meta { margin-bottom: 2rem; }
        .category-tag {
            display: inline-block;
            background: rgba(255, 75, 43, 0.1);
            color: var(--primary);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 3rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: #111;
        }

        .author-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #eee;
        }

        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .author-details p { font-size: 0.9rem; color: var(--gray); margin: 0; line-height: 1.4; }
        .author-details h4 { margin: 0; font-size: 1.1rem; }

        .post-content { font-size: 1.2rem; color: #333; }
        .post-content img { max-width: 100%; border-radius: 12px; margin: 2rem 0; }

        /* Comments Section */
        .comments-section {
            background: #f8faff;
            padding: 5rem 2rem;
            margin-top: 5rem;
        }

        .comments-container { max-width: 800px; margin: 0 auto; }

        .comment-form {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 4rem;
        }

        .comment-form h3 { margin-bottom: 1.5rem; font-family: 'Outfit', sans-serif; }

        .form-group { margin-bottom: 1.5rem; }
        input, textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #edf2f7;
            border-radius: 12px;
            outline: none;
            transition: border-color 0.3s;
            font-size: 1rem;
        }

        input:focus, textarea:focus { border-color: var(--primary); }

        .submit-comment {
            background: var(--dark);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }

        .submit-comment:hover { background: var(--primary); transform: translateY(-3px); }

        .comment-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            border: 1px solid #edf2f7;
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .comment-author { font-weight: 700; color: var(--dark); }

        /* Related Posts */
        .related-section { max-width: 800px; margin: 5rem auto; padding: 0 2rem; }
        .related-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; margin-top: 2rem; }
        .related-card {
            text-decoration: none;
            color: var(--dark);
        }
        .related-card h5 { font-size: 1.1rem; margin-top: 0.5rem; transition: color 0.3s; }
        .related-card:hover h5 { color: var(--primary); }

        @media (max-width: 768px) {
            h1 { font-size: 2rem; }
            article { margin: 2rem auto; }
        }
    </style>
</head>
<body>

    <div class="progress-bar"></div>

    <nav>
        <a href="javascript:void(0)" onclick="navigate('index.php')" class="back-btn">← Home</a>
        <div class="edit-actions">
            <a href="javascript:void(0)" onclick="navigate('edit.php?id=<?php echo $post['id']; ?>')" class="btn btn-edit">Edit Post</a>
            <button onclick="confirmDelete(<?php echo $post['id']; ?>)" class="btn btn-delete">Delete</button>
        </div>
    </nav>

    <article>
        <div class="post-meta">
            <span class="category-tag"><?php echo htmlspecialchars($post['category']); ?></span>
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        </div>

        <div class="author-card">
            <div class="avatar"><?php echo strtoupper(substr($post['author'], 0, 1)); ?></div>
            <div class="author-details">
                <h4><?php echo htmlspecialchars($post['author']); ?></h4>
                <p>Published on <?php echo date('F d, Y', strtotime($post['created_at'])); ?></p>
            </div>
        </div>

        <div class="post-content">
            <?php echo $post['content']; // HTML content from Quill ?>
        </div>
    </article>

    <section class="related-section">
        <h3>Related in <?php echo htmlspecialchars($post['category']); ?></h3>
        <div class="related-grid">
            <?php foreach ($related as $rel): ?>
                <a href="javascript:void(0)" onclick="navigate('post.php?id=<?php echo $rel['id']; ?>')" class="related-card">
                    <div style="height: 120px; background: #eee; border-radius: 10px; margin-bottom: 1rem;"></div>
                    <h5><?php echo htmlspecialchars($rel['title']); ?></h5>
                </a>
            <?php endforeach; ?>
            <?php if (empty($related)): ?>
                <p style="color: var(--gray);">No related posts yet.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="comments-section">
        <div class="comments-container">
            <div class="comment-form">
                <h3 id="comment-title">Leave a Comment</h3>
                <form id="comment-form">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <div class="form-group">
                        <input type="text" name="author" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <textarea name="comment" rows="4" placeholder="Share your thoughts..." required></textarea>
                    </div>
                    <button type="submit" class="submit-comment" id="comment-submit">Post Comment</button>
                </form>
            </div>

            <div id="comments-list">
                <h3>Comments (<?php echo count($comments); ?>)</h3>
                <br>
                <?php if (empty($comments)): ?>
                    <p style="text-align: center; color: var(--gray);">No comments yet. Be the first!</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-card">
                            <div class="comment-header">
                                <span class="comment-author"><?php echo htmlspecialchars($comment['author']); ?></span>
                                <span style="color: var(--gray);"><?php echo date('M d, Y', strtotime($comment['created_at'])); ?></span>
                            </div>
                            <div class="comment-text">
                                <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script>
        function navigate(url) {
            window.location.href = url;
        }

        // Progress bar logic
        window.addEventListener('scroll', () => {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            document.querySelector(".progress-bar").style.width = scrolled + "%";
        });

        // Comment submission
        document.getElementById('comment-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('comment-submit');
            btn.innerText = 'Posting...';
            btn.disabled = true;

            const formData = new FormData(this);

            try {
                const response = await fetch('process_comment.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if(result.status === 'success') {
                    // Refresh current page to see new comment
                    navigate('post.php?id=<?php echo $post['id']; ?>');
                } else {
                    alert('Error: ' + result.message);
                    btn.innerText = 'Post Comment';
                    btn.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                btn.innerText = 'Post Comment';
                btn.disabled = false;
            }
        });

        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this post?")) {
                navigate('delete.php?id=' + id);
            }
        }
    </script>
</body>
</html>
