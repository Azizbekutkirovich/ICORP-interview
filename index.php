<?php

class ApiTest
{
    // URL конечной точки API
	public $endpoint = "https://test.icorp.uz/private/interview.php";
    
    // Таймаут запроса в секундах
	public $timeout = 20;

    // Вспомогательный метод для проверки условий и генерации исключений
	private function checkingError($condition, $message) {
		if ($condition) {
			throw new Exception($message); // Бросаем исключение с сообщением об ошибке
		}
	}

    // Универсальный метод для отправки HTTP-запросов через cURL
	public function sendRequest($url, $method, $data = null) {
		$ch = curl_init(); // Инициализация cURL

        // Настройки cURL
		$options = [
			CURLOPT_RETURNTRANSFER => true, // Возвращать результат вместо вывода
			CURLOPT_TIMEOUT => $this->timeout, // Таймаут запроса
			CURLOPT_HTTPHEADER => ["Content-type" => "application/json"] // Заголовок JSON
		];

        // Настройка метода запроса
		switch ($method) {
			case "GET":
				if (!empty($data)) {
					$url .= "?".http_build_query($data); // Добавляем параметры в URL для GET
				}
				break;

			default:
				if ($method === "POST") {
					$options[CURLOPT_POST] = true; // Устанавливаем метод POST
				} else {
					$options[CURLOPT_CUSTOMREQUEST] = $method; // Пользовательский метод (PUT, DELETE и др.)
				}

				if (!empty($data)) {
					$options[CURLOPT_POSTFIELDS] = json_encode($data); // Передаем данные в формате JSON
				}
				break;
		}
		$options[CURLOPT_URL] = $url; // Устанавливаем URL
		curl_setopt_array($ch, $options); // Применяем настройки
		$response = curl_exec($ch); // Выполняем запрос
		
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Получаем HTTP код ответа
		$error = curl_error($ch); // Получаем ошибку cURL, если есть

		curl_close($ch); // Закрываем cURL-сессию и освобождаем ресурсы

        // Проверка ошибок
		$this->checkingError(!empty($error), "CURL Error: ".$error); // Ошибка cURL
		$this->checkingError($httpCode !== 200, "HTTP Error: ".$httpCode); // HTTP код не 200
		$this->checkingError($response === "", "От сервера получен пустой ответ"); // Пустой ответ сервера

        // Декодируем JSON
		$decoded = json_decode($response, true);
		$this->checkingError(json_last_error() !== JSON_ERROR_NONE, "Decode error: ".json_last_error_msg()." | Ответ: ".$response); // Проверка на корректность JSON
		return $decoded; // Возвращаем массив с ответом
	}

    // Первый запрос к API
	public function firstRequest() {
		$payload = [
			"msg" => "Hello", // Сообщение
			"uri" => "test"   // URI для идентификации запроса
		];
		$response = $this->sendRequest($this->endpoint, "POST", $payload); // Отправляем POST запрос
        // Проверяем наличие обязательных полей
		$this->checkingError(!isset($response['part1']) || !isset($response['uri']), "Неожиданный формат ответа в первом запросе - отсутствуют поля 'part1' или 'uri'");
		return $response;
	}

    // Второй запрос к URL, полученному из первого запроса
	public function secondRequest($url) {
		$response = $this->sendRequest($url, "GET"); // GET запрос
		$this->checkingError(!isset($response['part2']), "Неожиданный формат ответа во втором запросе - отсутствуют поле 'part2'"); // Проверка поля part2
		return $response;
	}

    // Финальный запрос с объединенным кодом
	public function finalRequest($code) {
		$payload = [
			'code' => $code // Передаем объединенный код
		];
		$response = $this->sendRequest($this->endpoint, "POST", $payload); // Отправляем POST запрос
		return $response;
	}

    // Основной метод для выполнения всей цепочки запросов
	public function execute() {
		try {
			$firstRequest = $this->firstRequest(); // Первый запрос
			$firstPart = $firstRequest['part1']; // Получаем part1
			$next_url = $firstRequest['uri']; // Получаем следующий URL
			$secondRequest = $this->secondRequest($next_url); // Второй запрос
			$secondPart = $secondRequest['part2']; // Получаем part2
			$fullCode = $firstPart.$secondPart; // Объединяем части кода
			$finalRequest = $this->finalRequest($fullCode); // Финальный запрос
			return $finalRequest;
		} catch (Exception $e) {
			return "Ошибка: ".$e->getMessage(); // В случае ошибки возвращаем сообщение
		}
	}
}

// Создаем объект и выполняем цепочку запросов
$client = new ApiTest();
$result = $client->execute();

print_r($result); // Выводим результат