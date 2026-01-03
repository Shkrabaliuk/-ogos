<h1>Пошук</h1>

<form method="GET" action="/search.php">
    <label>Запит
        <input type="search" name="q" placeholder="Введіть запит..." value="<?= htmlspecialchars($q) ?>" autofocus
            required>
    </label>
    <button type="submit">Шукати</button>
</form>

<?php if (isset($error)): ?>
    <section>
        <p style="color:red">⚠
            <?= htmlspecialchars($error) ?>
        </p>
    </section>
<?php endif; ?>

<?php if (!empty($q)): ?>
    <?php if (!empty($results)): ?>
        <p>Знайдено: <strong>
                <?= count($results) ?>
            </strong></p>
        <hr>

        <?php foreach ($results as $result): ?>
            <article>
                <header>
                    <h2>
                        <a href="/<?= htmlspecialchars($result['slug']) ?>">
                            <?= htmlspecialchars($result['title']) ?>
                        </a>
                    </h2>
                </header>

                <div>
                    <?= $result['snippet'] ?>
                </div>

                <footer>
                    <small>
                        <?= date('d.m.Y', strtotime($result['date'])) ?>
                        • Релевантність:
                        <?= round($result['relevance'], 2) ?>
                    </small>
                </footer>
            </article>
            <hr>
        <?php endforeach; ?>

    <?php else: ?>
        <section>
            <h2>Нічого не знайдено</h2>
            <p>За запитом «
                <?= htmlspecialchars($q) ?>» результатів немає.
            </p>
            <p><a href="/">← Повернутися на головну</a></p>
        </section>
    <?php endif; ?>
<?php endif; ?>