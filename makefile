lint: #запуск phpcs
	composer exec --verbose phpcs -- --standard=PSR12 src bin
test: #запуск локального теста
	composer exec --verbose phpunit tests