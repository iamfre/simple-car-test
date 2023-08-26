[![Typing SVG](https://readme-typing-svg.herokuapp.com?color=%2336BCF7&lines=Test+task)](https://git.io/typing-svg)
# Instalation

1. git clone
2. git submodule init && git submodule update
3. cp .env.example .env
4. php artisan migrate
5. composer install
6. php artisan storage:link

# Функционал
* GET /api/cars - получить список
* POST /api/cars
  * actions : 
    * update - создать новую или обновить запись по id или external_id
    * delete - удалить запись по id или external_id
* GET /api/cars/{id} - получить запись по id или external_id

# Examples
postman fixtures - https://documenter.getpostman.com/view/18929428/2s9Y5YS2c8