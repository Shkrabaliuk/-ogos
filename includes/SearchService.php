<?php
/**
 * Search Service — обгортка для Rose fulltext пошуку
 */

require_once __DIR__ . '/../config/autoload.php';
require_once __DIR__ . '/../config/db.php';

use S2\Rose\Indexer;
use S2\Rose\Finder;
use S2\Rose\SnippetBuilder;
use S2\Rose\Stemmer\PorterStemmerRussian;
use S2\Rose\Storage\Database\PdoStorage;
use S2\Rose\Entity\Indexable;
use S2\Rose\Entity\Query;

class SearchService
{
    private $pdo;
    private $stemmer;
    private $storage;
    
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->stemmer = new PorterStemmerRussian();
        $this->storage = new PdoStorage($pdo, 'rose_');
    }
    
    /**
     * Індексація одного поста
     */
    public function indexPost($post)
    {
        $indexer = new Indexer($this->storage, $this->stemmer);
        
        // Створюємо indexable entity
        $externalId = 'post_' . $post['id'];
        $url = '/' . $post['slug'];
        
        // Комбінуємо заголовок і контент для індексації
        $content = strip_tags($post['content']);
        
        $indexable = new Indexable(
            $externalId,
            $post['title'],
            $content
        );
        
        $indexable
            ->setUrl($url)
            ->setDescription(mb_substr($content, 0, 200))
            ->setDate(new \DateTime($post['created_at']));
        
        // Додаємо до індексу
        $indexer->index($indexable);
        
        return true;
    }
    
    /**
     * Переіндексація всіх постів
     */
    public function reindexAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM posts WHERE is_published = 1");
        $posts = $stmt->fetchAll();
        
        $count = 0;
        foreach ($posts as $post) {
            $this->indexPost($post);
            $count++;
        }
        
        return $count;
    }
    
    /**
     * Пошук по запиту
     */
    public function search($queryString, $limit = 10)
    {
        if (empty(trim($queryString))) {
            return [];
        }
        
        $finder = new Finder($this->storage, $this->stemmer);
        $finder->setHighlightTemplate('<mark>%s</mark>');
        
        $snippetBuilder = new SnippetBuilder($this->stemmer);
        $snippetBuilder->setSnippetLineSeparator(' … ');
        
        // Створюємо query
        $query = Query::fromString($queryString);
        
        // Шукаємо
        $resultSet = $finder->find($query);
        
        $results = [];
        $items = $resultSet->getItems();
        
        // Обмежуємо кількість результатів
        $items = array_slice($items, 0, $limit);
        
        foreach ($items as $item) {
            $externalId = $item->getId()->toString();
            
            // Витягуємо ID поста
            if (preg_match('/^post_(\d+)$/', $externalId, $matches)) {
                $postId = $matches[1];
                
                // Отримуємо пост з БД
                $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE id = ?");
                $stmt->execute([$postId]);
                $post = $stmt->fetch();
                
                if ($post) {
                    // Генеруємо snippet з виділенням
                    $snippet = $snippetBuilder->buildSnippet(
                        $query,
                        $item,
                        strip_tags($post['content'])
                    );
                    
                    $results[] = [
                        'id' => $post['id'],
                        'title' => $post['title'],
                        'slug' => $post['slug'],
                        'snippet' => $snippet->toString(),
                        'relevance' => $item->getRelevance(),
                        'date' => $post['created_at']
                    ];
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Видалення поста з індексу
     */
    public function removePost($postId)
    {
        $externalId = 'post_' . $postId;
        
        // Видаляємо через raw SQL (Rose не має простого методу видалення)
        $this->pdo->prepare("DELETE FROM rose_toc WHERE external_id = ?")->execute([$externalId]);
        
        return true;
    }
}
