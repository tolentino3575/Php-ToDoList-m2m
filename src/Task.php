<?php
class Task
{
    private $description;
    private $id;
    private $done;


    function __construct($description, $id = null, $done = false)
    {
      $this->description = $description;
      $this->id = $id;
      $this->done = $done;
    }
    function getId()
    {
      return $this->id;
    }

    function setDone($new_done)
    {
      $this->done = (string) $new_done;
    }

    function getDone()
    {
      return $this->done;
    }

    function setDescription($new_description)
    {
      $this->description = (string) $new_description;
    }
    function getDescription()
    {
      return $this->description;
    }
    function save()
    {
      $GLOBALS['DB']->exec("INSERT INTO tasks (description, done) VALUES ('{$this->getDescription()}', {$this->getDone()});");
      $this->id = $GLOBALS['DB']->lastInsertId();
    }
    static function getAll()
    {
      $returned_tasks = $GLOBALS['DB']->query("SELECT * FROM tasks ORDER BY description");

      $tasks = array();
      foreach($returned_tasks as $task) {
          $description = $task['description'];
          $id = $task['id'];
          $done = $task['done'];
          $new_task = new Task($description, $id, $done);
          array_push($tasks, $new_task);
      }
      return $tasks;
    }

    function updateDone()
    {
      $GLOBALS['DB']->exec("UPDATE tasks SET done = '1' WHERE id = {$this->getId()};");
      $this->setDone('1');
    }

    function addCategory($category)
    {
      $GLOBALS['DB']->exec("INSERT INTO categories_tasks (category_id, task_id) VALUES ({$category->getId()}, {$this->getId()});");
    }

    function getCategories()
    {
        $query = $GLOBALS['DB']->query("SELECT category_id FROM categories_tasks WHERE task_id = {$this->getId()};");
        $category_ids = $query->fetchAll(PDO::FETCH_ASSOC);

        $categories = array();
        foreach($category_ids as $id){
          $category_id = $id['category_id'];
          $result = $GLOBALS['DB']->query("SELECT * FROM categories WHERE id = {$category_id};");

          $returned_category = $result->fetchAll(PDO::FETCH_ASSOC);
          $name = $returned_category[0]['name'];
          $id = $returned_category[0]['id'];
          $new_category = new Category($name, $id);
          array_push($categories, $new_category);
        }
        return $categories;
    }
    static function deleteAll()
    {
       $GLOBALS['DB']->exec("DELETE FROM tasks;");
    }
    static function find($search_id)
    {
      $found_task = null;
      $tasks = Task::getAll();
      foreach($tasks as $task)
      {
        $task_id = $task->getId();
        if ($task_id == $search_id)
          {
            $found_task = $task;
          }
      } return $found_task;
    }

    function delete()
    {
      $GLOBALS['DB']->exec("DELETE FROM tasks WHERE id = {$this->getId()};");
      $GLOBALS['DB']->exec("DELETE FROM categories_tasks WHERE task_id = {$this->getId()};");
    }

}
?>
