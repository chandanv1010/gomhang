# Sửa menu và khắc phục mục con bị lỗi 404

Có 2 nhóm menu đang dùng:

| Nhóm | Từ khoá | Hiện ở đâu |
|---|---|---|
| Menu chính | `main-menu` | thanh menu trên đầu trang |
| Menu chân trang | `footer-menu` | các cột bên phải ở chân trang |

---

## Phần 1 — Sửa menu

### Bước 1 — Mở nhóm menu

Menu trái: **QL Menu → Cài đặt Menu**. Bấm nút sửa ở nhóm muốn đổi.

Màn hình hiện ra dạng cây: mục cấp 1 nằm ngoài, mục con thụt vào trong.

### Bước 2 — Sửa mục cấp 1

Bấm **"Cập nhật Menu cấp 1"** ở góc phải khung.

Mỗi dòng có 3 ô:

| Ô | Nghĩa | Ví dụ |
|---|---|---|
| Tên | chữ hiện ra ngoài site | `Sản phẩm` |
| Đường dẫn | phần địa chỉ, **không có** `https://` và **không có** `.html` | `phu-kien-dien-thoai` |
| Thứ tự | số càng **lớn** thì đứng càng **trước** | `4` |

> **Lưu ý về thứ tự:** hệ thống sắp xếp **giảm dần**. Muốn "Trang chủ" đứng đầu thì
> nó phải mang số lớn nhất. Đánh số xuôi 1-2-3-4 sẽ ra menu ngược.
>
> **Cách dễ hơn:** ở màn hình cây (Bước 1) có thể **kéo thả** trực tiếp các dòng để
> đổi vị trí, khỏi phải tính số. Kéo xong hệ thống tự lưu, không cần bấm Lưu.

- Thêm dòng: bấm **"Thêm đường dẫn"**
- Xoá dòng: bấm dấu **✕** cuối dòng
- Để trống ô Tên: dòng đó bị bỏ qua khi lưu, không gây lỗi

Bấm **Lưu**.

### Bước 3 — Sửa mục con

Ở màn hình cây, bấm **"Quản lý menu con"** trên dòng mục cha. Cách điền giống hệt
Bước 2.

Riêng **Menu chân trang**: mỗi mục cấp 1 là **một cột** ở chân trang, các mục con
là các link trong cột đó. Thêm mục cấp 1 = thêm một cột mới.

---

## Phần 2 — Vì sao 4 mục ở chân trang bị 404

4 link này đang lỗi:

- Chính sách vận chuyển → `chinh-sach-van-chuyen`
- Chính sách bảo mật → `chinh-sach-bao-mat`
- Chính sách bảo hành → `chinh-sach-bao-hanh`
- Địa chỉ shop → `dia-chi-shop`

**Nguyên nhân không phải thiếu danh mục bài viết.** Trong hệ thống này, địa chỉ
trang được sinh ra từ 4 nguồn:

| Nguồn | Số trang đang có |
|---|---|
| Sản phẩm | 65 |
| Danh mục sản phẩm | 64 |
| **Bài viết** | **4** |
| Danh mục bài viết | 1 |

4 đường dẫn trên **chưa có bài viết nào mang đường dẫn đó**, nên vào là 404. Menu
trỏ đúng, chỉ là trang đích chưa tồn tại.

### Cách sửa — tạo bài viết cho từng trang

**QL Bài viết → Thêm mới bài viết**, rồi:

1. **Tiêu đề**: `Chính sách vận chuyển`
2. **Nội dung**: soạn nội dung chính sách
3. Kéo xuống mục SEO, tìm ô **"Đường dẫn (không bao gồm đuôi .html)"**
4. Điền **chính xác**: `chinh-sach-van-chuyen`
   — không dấu, không khoảng trắng, không `.html`, không dấu `/` ở đầu
5. Chọn **Nhóm bài viết** bất kỳ
6. Bật **Tình trạng** sang *Hiển thị*
7. **Lưu**

Làm tương tự cho 3 trang còn lại với đúng đường dẫn ở bảng trên.

Xong bước này, vào lại `/chinh-sach-van-chuyen.html` sẽ ra trang thay vì 404.

> **Quan trọng:** đường dẫn trong bài viết phải **khớp từng ký tự** với đường dẫn
> trong menu. Sai một dấu gạch ngang là vẫn 404.

### Cách khác — đổi menu trỏ sang trang đã có

Nếu chưa muốn viết nội dung chính sách, có thể tạm bỏ 4 mục đó khỏi menu chân
trang (Bước 3 ở trên, bấm ✕), để chân trang không còn link chết.

---

## Kiểm tra lại

Sau khi sửa, bấm thử từng link ở chân trang. Link nào ra trang thật là đạt; còn
hiện "404 Not Found" nghĩa là đường dẫn trong bài viết chưa khớp với menu.
