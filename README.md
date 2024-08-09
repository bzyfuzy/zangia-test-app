# Шалгалтын систем

## Docker
```sh
docker compose up --build
```

## Local
```sh
php -S localhost:8080 -t public/
```

## Back-end

App/App usage. /BzY-Route PHP micro Framework/

```php

$app = new App();

$app->get('/', function (Request $request, Response $response) {
    $response->send([
        "status" => "success",
        "result" => "hello world!"
    ]);
});

$app->get('/{some_param}', function (Request $request, Response $response) {
    $response->send("Result: " . $request->params["some_param"]);
});

$app->get("/handle-from-conroller/{id}", "SomeController::handler_method");

$app->get('/render-from-views', function ($request, $response) {
    $response->render("view-file", ["title" => "render-view-file"]);
});

$app->get('/private', function (Request $request, Response $response, [Auth::checkAuth()]) {
    $response->send("Result: " . $request->me);
});

$app->run();

```

App/Models usage. /BzY-ODM PHP micro Framework/

```php
#Model
class Answer extends DBModel {
    public string $title;
    public string $publisher;
    public string $content;

    protected static string $tableName = 'articles';
    protected static array $nullable = ['content];
    protected static array $uniques = ['title'];
}

#usage
use App/Models/Article
$article = new Article(["title" => "The Article", "publisher" => "BzY*FuZY", "content"=> "The long long text"]);
$article->save();
$newArticleInsertedID = $article->id;

$article = Article::findOne(["publisher" => "BzY*FuZY"]);
$article = Article::findOneByID($newArticleInsertedID);
$articles = Article::find(["publisher" => "BzY*FuZY"]);

```
