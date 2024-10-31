## 資料庫測驗

**題目一：**

    ```bash
        SELECT o.bnb_id AS bnb_id, b.name AS bnb_name, SUM(o.amount) AS may_amount
        FROM  orders o
        JOIN  bnbs b ON o.bnb_id = b.id
        WHERE  o.currency = 'TWD' AND o.created_at BETWEEN '2023-05-01' AND '2023-05-31'
        GROUP BY o.bnb_id, b.name
        ORDER BY may_amount DESC
        LIMIT 10;

**題目二**
    - 1. 假設是 orders 的資料過於龐大，我會先對 orders 做 partition table 然後以時間作為區分。然後對資料庫做基本的檢查：常被用於查詢的欄位有沒做 index，各欄位的型別是不是最佳的，有無做正規化，以及有無冗余資料。
    - 2. 假設肇因為高迸發，因為 mysql 在高壓時會有所競爭的瓶頸，這時候可能要考慮換 postgre 這類事務能力較強的資料庫。
    - 3. 設置 Redis 減少資料庫訪問，尤其這裡的表看起來變動的頻率不會很高，Redis 會有很大的發揮空間。

---

## API 實作測驗 - 設計原則

### 1. 單一職責原則 (SRP)
- **目標**：每個類別應只有一個變更的原因。
- **實踐**： 
  - 將驗證邏輯集中在 `OrderRequest`，專注於處理請求的資料驗證。
  - 業務邏輯則位於 `OrderService`，象徵性地處理與匯率轉換相關的需求。
  - 此專案未涉及資料庫操作，因此未設置 Repository。

### 2. 開放封閉原則 (OCP)
- **目標**：軟體應對擴展開放，對修改封閉。
- **實踐**：
  - 在 `OrderRequest` 的 `prepareForValidation` 方法中，將換匯邏輯獨立於 `currencyTrans` 方法。
  - 如未來有其他預處理需求，可直接添加至此方法中，而無需更改現有邏輯。

### 3. 里氏替換原則 (LSP)
- **目標**：任何子類別應可以替換其父類別。
- **實踐**：
  - 本專案邏輯的執行過程中未違反父類別的行為約定，且後續處理也不會推翻先前的驗證結果。

### 4. 方法分離
- **實踐**：
  - 在 `prepareForValidation` 中，將換匯邏輯抽出為一個方法，若有其他需求可同樣進行方法分離，以提升可讀性和維護性。

### 5. 依賴反轉原則 (DIP)
- **目標**：高階模組不應依賴於低階模組；兩者都應依賴於抽象。
- **實踐**：
  - Controller 並未直接依賴具體的服務操作，而是通過 `use` 方式使用 `OrderService`，便於單元測試及維護。

---

## 使用提示

1. **環境設定**  
   此專案未使用資料庫，直接將 `.env.example` 複製並重新命名為 `.env` 即可。

2. **專案初始化**  
   在 clone 完成後，請執行以下命令來安裝相關依賴並啟動專案：

   ```bash
   composer install
   php artisan key:generate
   docker-compose up -d --build
