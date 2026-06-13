# Chapter 6: Frontend Integration & User Interface

## 6.1 Server-Side Rendering (SSR) & cURL Integration
The Bookshop Management System's frontend operates as a Server-Side Rendered (SSR) PHP application (hosted on Port `8081`). Rather than executing direct MySQL queries or importing model classes locally, the frontend makes HTTP calls to the backend microservice ports.

This integration is managed by a centralized cURL handler inside `frontend/components/config.php`:
```php
function makeServiceRequest(string $url, string $method = 'GET', ?array $data = null): ?array {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    if ($method === 'GET' && !empty($data)) {
        $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($data);
    }
    
    curl_setopt($ch, CURLOPT_URL, $url);
    
    if (in_array($method, ['POST', 'PUT', 'DELETE']) && $data !== null) {
        $jsonData = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}
```

## 6.2 Session Management & Identity Guarding
* **State Persistence:** When a user logs in, the User Service validates credentials and returns account metadata (email, fullname, role). The frontend server saves these values in standard session cookies (`$_SESSION`).
* **Route Protection:** Access controls are checked on the frontend before loading templates. Staff pages (e.g. `inventory.php`, `orders.php`) verify if the logged-in user has the `staff` or `admin` role, redirecting unauthorized users to `login.php`.

## 6.3 Dynamic UI Views
The user interface utilizes Bootstrap 5, custom CSS stylings (`shared.css`), and JavaScript handlers (`shared.js`) to display system alerts, warning notifications, and interactive analytics.
* **Storefront (`books.php`):** Queries catalog and inventory services, rendering a responsive grid of book cards. Buttons are automatically disabled if stock is unavailable.
* **Staff Inventory Control (`inventory.php`):** Displays current quantities, highlighting low-stock items with red warning badges and pulsing indicators.
* **Admin Dashboard (`admin.php`):** Pulls metric summaries (Total Revenue, low stock counts, log files) and displays visual charts using Chart.js.
