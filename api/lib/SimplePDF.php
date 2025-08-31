<?php

class SimplePDF {
    private array $entries = [];
    private int $defaultFontSize = 12;
    private int $startX = 50;
    private int $startY = 800;

    public function addTitle(string $text, int $size = 26): void {
        $this->entries[] = ['type' => 'text', 'text' => $this->escapeText($text), 'size' => $size];
    }

    public function addLine(string $text, ?int $size = null): void {
        $this->entries[] = ['type' => 'text', 'text' => $this->escapeText($text), 'size' => $size ?? $this->defaultFontSize];
    }

    public function addBlankLine(): void {
        $this->entries[] = ['type' => 'blank'];
    }

    private function escapeText(string $text): string {
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        // Replace non-ASCII with '?'
        $text = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '?', $text);
        return $text;
    }

    public function output(): string {
        $initialSize = $this->defaultFontSize;
        $initialTL = $initialSize + 4;
        $content = "BT\n/F1 {$initialSize} Tf\n{$this->startX} {$this->startY} Td\n{$initialTL} TL\n";
        $firstPrinted = false;
        $currentSize = $initialSize;
        foreach ($this->entries as $entry) {
            if ($entry['type'] === 'blank') {
                $content .= "T*\n";
                continue;
            }
            $size = (int)($entry['size'] ?? $this->defaultFontSize);
            if ($size !== $currentSize) {
                $currentSize = $size;
                $lineH = $currentSize + 4;
                $content .= "/F1 {$currentSize} Tf\n{$lineH} TL\n";
            }
            if (!$firstPrinted) {
                $content .= "(".$entry['text'].") Tj\n";
                $firstPrinted = true;
            } else {
                $content .= "T* (".$entry['text'].") Tj\n";
            }
        }
        $content .= "ET\n";

        $objects = [];
        $objects[] = "1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj\n";
        $objects[] = "2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1 >>endobj\n";
        $objects[] = "3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>endobj\n";
        $objects[] = "4 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj\n";
        $len = strlen($content);
        $objects[] = "5 0 obj<< /Length {$len} >>stream\n{$content}endstream\nendobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= $obj;
        }
        $xrefPos = strlen($pdf);
        $count = count($objects) + 1;
        $pdf .= "xref\n0 {$count}\n";
        $pdf .= sprintf("%010d %05d f\n", 0, 65535);
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d %05d n\n", $offsets[$i], 0);
        }
        $pdf .= "trailer<< /Size {$count} /Root 1 0 R >>\nstartxref\n{$xrefPos}\n%%EOF";
        return $pdf;
    }
}



