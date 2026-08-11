# Đổi khối danh mục sản phẩm ở trang chủ (ngay dưới slide)

Khối các ô tròn nằm dưới ảnh slide ở trang chủ. **Hiện ra đúng những danh mục
được tick trong widget** — tick 3 cái thì hiện 3 cái, tick 10 cái thì hiện 10 cái,
theo đúng thứ tự tick.

---

## Các bước

### Bước 1 — Vào danh sách widget

Menu trái: **QL Marketing → Widget** (hoặc vào thẳng `/widget/index`).

Trong bảng, tìm dòng có **Từ khóa = `solution-product`**, tên hiển thị là
**"Sản phẩm"**. Bấm nút sửa (biểu tượng bút chì màu xanh) ở cột *Thao tác*.

> Đừng nhầm với widget **`si-le-cong-nghe`** — cái đó là khối *"Sỉ lẻ hàng công
> nghệ"* liệt kê từng sản phẩm, không phải khối danh mục.

### Bước 2 — Chọn loại nội dung

Trong ô **"Chọn Module"** có 4 lựa chọn:

| Lựa chọn | Nghĩa |
|---|---|
| Nhóm Bài Viết | danh mục bài viết |
| Bài Viết | từng bài viết |
| **Nhóm Sản Phẩm** | **danh mục sản phẩm — đang dùng cái này** |
| Sản Phẩm | từng sản phẩm |

Giữ nguyên **Nhóm Sản Phẩm**. Đổi sang loại khác thì danh sách đã chọn bên dưới
sẽ bị xoá sạch.

### Bước 3 — Thêm hoặc bớt danh mục

- **Thêm:** gõ tên danh mục vào ô có biểu tượng kính lúp. Danh sách gợi ý hiện ra,
  bấm vào tên danh mục muốn thêm. Nó sẽ nhảy xuống danh sách bên dưới.
- **Bớt:** bấm dấu **✕** ở bên phải dòng danh mục đó.

**Về thứ tự hiển thị:** màn hình này **không kéo thả được**. Thứ tự ngoài trang chủ
đi theo thứ tự các danh mục được chọn. Muốn xếp lại, bấm ✕ bỏ hết rồi chọn lại
theo đúng thứ tự mong muốn.

Hiện đang chọn 3 danh mục: *Phụ kiện theo chủng loại*, *Phụ kiện iPhone*,
*Phụ kiện Samsung*.

### Bước 4 — Lưu

Bấm **Lưu** ở cuối trang. Ra ngoài trang chủ tải lại (Ctrl+F5) để xem kết quả.

---

## Ảnh trong ô tròn bị trắng thì làm sao

Ảnh của mỗi ô tròn lấy từ **ảnh đại diện của chính danh mục đó**, không phải đặt
trong widget. Danh mục nào chưa có ảnh thì ô tròn hiện logo website thay thế.

Để đặt ảnh: **QL Sản Phẩm → Nhóm sản phẩm →** sửa danh mục đó → mục **Ảnh đại diện**.

---

## Hay gặp

**Sửa xong mà ngoài trang chủ không đổi.**
Bấm Ctrl+F5 để trình duyệt tải lại. Nếu vẫn vậy, nhờ kỹ thuật chạy
`php artisan view:clear` trên máy chủ.

**Tick 3 danh mục mà ngoài trang hiện 9 cái.**
Đây là lỗi cũ đã sửa ngày 10/08/2026. Nếu còn gặp thì máy chủ chưa cập nhật code
mới nhất.
