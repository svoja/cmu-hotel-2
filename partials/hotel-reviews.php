<div class="card-body">
    <h6 class="fw-bold">Reviews</h6>
    <?php if ($review_stats['total_reviews'] > 0): ?>
        <p class="mb-1"><strong><?= number_format($review_stats['avg_rating'], 1) ?></strong> / 5.0 (<?= $review_stats['total_reviews'] ?> reviews)</p>
        <?php if (!empty($good_reviews)): ?>
            <ul class="list-unstyled">
                <?php foreach ($good_reviews as $review): ?>
                    <li class="mb-3">
                        <strong><?= htmlspecialchars($review['reviewer_name']) ?></strong> 
                        - <?= number_format($review['rating'], 1) ?> <span class="text-muted">/ 5.0</span>
                        <br>
                        <small class="text-muted">
                            "<?= htmlspecialchars($review['review_text']) ?>"
                        </small>
                        <br>
                        <small class="text-muted fst-italic">
                            <?= date("F j, Y", strtotime($review['created_at'])) ?> <!-- ✅ Format Date -->
                        </small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">No highly rated reviews available.</p>
        <?php endif; ?>
    <?php else: ?>
        <p class="text-muted">No reviews yet.</p>
    <?php endif; ?>
</div>