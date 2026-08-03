<?php
namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CustomerExport
{
    protected $customers;

    public function __construct($customers)
    {
        $this->customers = $customers;
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set default font
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(13);

        // Tiêu đề
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'Danh sách khách hàng');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $headings = [
            'STT', 
            'Họ và tên',
            'Email',
            'Số điện thoại',
            'Địa chỉ',
            'Nhóm khách hàng',
            'Nguồn khách'
        ];
        $sheet->fromArray($headings, null, 'A3');
        $sheet->getStyle('A3:G3')->getFont()->setBold(true);
        $sheet->getStyle('A3:G3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:G3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Ghi dữ liệu
        $row = 4;
        $index = 1;

        foreach ($this->customers as $customer) {
            $sheet->setCellValue('A' . $row, $index);
            $sheet->setCellValue('B' . $row, $customer->name ?? '');
            $sheet->setCellValue('C' . $row, $customer->email ?? '');
            $sheet->setCellValue('D' . $row, $customer->phone ?? '');
            $sheet->setCellValue('E' . $row, $customer->address ?? '');
            $sheet->setCellValue('F' . $row, $customer->group->name ?? '');
            $sheet->setCellValue('G' . $row, $customer->source->name ?? '');

            $index++;
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A3:G$lastRow")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Căn chỉnh
        $sheet->getStyle("A3:G$lastRow")->getAlignment()->setWrapText(true);
        $sheet->getStyle("A3:A$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C3:C$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("B3:B$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("E3:E$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Auto width
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Tạo thư mục temp nếu chưa có
        $tempDir = public_path('temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $filename = 'Danh_sach_khach_hang.xlsx';

        $filePath = $tempDir . '/' . $filename;

        $writer = new Xlsx($spreadsheet);

        $writer->save($filePath);

        return $filePath;
    }
}
