<?php
namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class OrderExport
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Set default font
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(13);
        // Tiêu đề
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'Danh sách đơn hàng');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // Header
        $headings = [
            'Mã đơn hàng', 
            'Ngày tạo',
            'Khách hàng',
            'Số điện thoại',
            'Địa chỉ',
            'Tổng cuối',
            'Trạng thái',
            'Giao hàng',
            'Hình thức'
        ];
        $sheet->fromArray($headings, null, 'A3');
        $sheet->getStyle('A3:G3')->getFont()->setBold(true);
        $sheet->getStyle('A3:G3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:G3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        // Ghi dữ liệu
        $row = 4;
        $index = 1;
        foreach ($this->orders as $order) {
            $sheet->setCellValue('A' . $row, $order->code);
            $sheet->setCellValue('B' . $row, $order->created_at ?? '');
            $sheet->setCellValue('C' . $row, $order->fullname ?? '');
            $sheet->setCellValue('D' . $row, $order->phone ?? '');
            $sheet->setCellValue('E' . $row, $order->address ?? '');
            $sheet->setCellValue('F' . $row, $order->total ?? '2300000đ');
            $sheet->setCellValue('G' . $row, $order->group->name ?? 'Chờ thanh toán');
            $sheet->setCellValue('H' . $row, $order->group->name ?? 'Chưa giao');
            $sheet->setCellValue('I' . $row, $order->method ?? '');
            $index++;
            $row++;
        }
        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A3:I$lastRow")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Căn chỉnh
        $sheet->getStyle("A3:I$lastRow")->getAlignment()->setWrapText(true);
        $sheet->getStyle("A3:A$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C3:C$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("B3:B$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("E3:E$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Auto width
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Tạo thư mục temp nếu chưa có
        $tempDir = public_path('temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $filename = 'Danh_sach_don_hang.xlsx';

        $filePath = $tempDir . '/' . $filename;

        $writer = new Xlsx($spreadsheet);

        $writer->save($filePath);

        return $filePath;
    }
}
