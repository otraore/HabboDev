<?php

$app->get('/api/projects', function () use ($app) {
    $request = $app->request;
    $query = $request->get('query');

    $app->response->header('Content-Type:', 'application/json');

    if (!isset($query) || empty($query)) {
        die(json_encode(['errors' => 'empty query']));
    } else {
        $db = new PDO('mysql:host=127.0.0.1;dbname=site;', 'root', 'mypass123');

        $projects = $db->prepare('
        SELECT user_id, project_name, type
        FROM projects
        WHERE project_name LIKE :query
        ');

        $projects->execute([
            'query' => $query . '%'
        ]);

        echo json_encode($projects->fetchAll(PDO::FETCH_ASSOC));
    }
});