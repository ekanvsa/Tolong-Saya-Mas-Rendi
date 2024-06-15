<?php

class AppError extends Exception {
}

function i2bin($i, $l) {
    $actual = decbin($i);
    if (strlen($actual) > $l) {
        throw new AppError("bit size is larger than expected.");
    }

    while (strlen($actual) < $l) {
        $actual = "0" . $actual;
    }

    return $actual;
}

function char2bin($c) {
    return i2bin(ord($c), 8);
}

class LSB {
    const MAX_BIT_LENGTH = 16;
    private $image;
    private $size_x;
    private $size_y;
    private $cur_x;
    private $cur_y;
    private $cur_channel;

    public function __construct($imgPath) {
        $this->image = imagecreatefrompng($imgPath);
        $this->size_x = imagesx($this->image);
        $this->size_y = imagesy($this->image);
        $this->cur_x = 0;
        $this->cur_y = 0;
        $this->cur_channel = 0;
    }

    private function next() {
        if ($this->cur_channel != 2) {
            $this->cur_channel += 1;
        } else {
            $this->cur_channel = 0;
            if ($this->cur_y != $this->size_y - 1) {
                $this->cur_y += 1;
            } else {
                $this->cur_y = 0;
                if ($this->cur_x != $this->size_x - 1) {
                    $this->cur_x += 1;
                } else {
                    throw new AppError("need larger image");
                }
            }
        }
    }

    private function put_bit($bit) {
        $rgb = imagecolorat($this->image, $this->cur_x, $this->cur_y);
        $colors = imagecolorsforindex($this->image, $rgb);
        $colorChannel = ['red', 'green', 'blue'][$this->cur_channel];
        $v = $colors[$colorChannel];
        $binaryV = decbin($v);

        if (strlen($binaryV) < 8) {
            $binaryV = str_pad($binaryV, 8, "0", STR_PAD_LEFT);
        }

        if ($binaryV[-1] != $bit) {
            $binaryV[-1] = $bit;
            $colors[$colorChannel] = bindec($binaryV);
            $newColor = imagecolorallocate($this->image, $colors['red'], $colors['green'], $colors['blue']);
            imagesetpixel($this->image, $this->cur_x, $this->cur_y, $newColor);
        }

        $this->next();
    }

    private function put_bits($bits) {
        foreach (str_split($bits) as $bit) {
            $this->put_bit($bit);
        }
    }

    private function read_bit() {
        $rgb = imagecolorat($this->image, $this->cur_x, $this->cur_y);
        $colors = imagecolorsforindex($this->image, $rgb);
        $colorChannel = ['red', 'green', 'blue'][$this->cur_channel];
        $v = $colors[$colorChannel];
        $this->next();
        return decbin($v)[-1];
    }

    private function read_bits($length) {
        $bits = "";
        for ($i = 0; $i < $length; $i++) {
            $bits .= $this->read_bit();
        }
        return $bits;
    }

    public function embed($text) {
        echo "\n\n====================================================================\n";
        echo "Proses Penyisipan Pesan Menggunakan LSB\n";
        echo "====================================================================\n";
        echo "\nCipherText Yang Akan Disisipkan : \n{$text}\n";
        
        $text_length = i2bin(strlen($text), self::MAX_BIT_LENGTH);
        $this->put_bits($text_length);
        
        foreach (str_split($text) as $i => $c) {
            $char_binary = char2bin($c);
            if ($i < 2 || $i >= strlen($text) - 2) {
                echo "\n\n=============================================\n";
                echo "Peletakkan Karakter '{$c}'  ke Gambar\n";
                echo "=============================================\n";
                echo "Converting character '{$c}' to binary: {$char_binary}\n";
            }

            foreach (str_split($char_binary) as $bit) {
                $binary_pixel_value_before_embedding = decbin(imagecolorat($this->image, $this->cur_x, $this->cur_y));
                $this->put_bit($bit);
                if ($i < 2 || $i >= strlen($text) - 2) {
                    echo "\n\nPutting bit into image: {$bit}\n";
                }
                $binary_pixel_value_after_embedding = decbin(imagecolorat($this->image, $this->cur_x, $this->cur_y));
                if ($i < 2 || $i >= strlen($text) - 2) {
                    echo "Current Pointer Position: {$this->cur_x}, {$this->cur_y}, {$this->cur_channel}\n";
                    echo "Embedding Bit: {$bit}\n";
                    echo "Pixel Value Before Embedding: " . bindec($binary_pixel_value_before_embedding) . "\n";
                    echo "Binary Value Before Embedding: {$binary_pixel_value_before_embedding}\n";
                    echo "Pixel Value After Embedding: " . bindec($binary_pixel_value_after_embedding) . "\n";
                    echo "Binary Value After Embedding: {$binary_pixel_value_after_embedding}\n";
                }
            }

            if ($i < 2 || $i >= strlen($text) - 2) {
                echo "Embedding character " . ($i + 1) . "/" . strlen($text) . ": '{$c}'\n";
            }
        }

        echo "Embedding complete\n";
    }

    public function extract() {
        echo "====================================================================\n";
        echo "Proses Ekstrak Pesan Menggunakan LSB\n";
        echo "====================================================================\n";
        
        $length_bits = $this->read_bits(self::MAX_BIT_LENGTH);
        $length = bindec($length_bits);
        echo "Binary Length Bits: {$length_bits}\n";
        echo "Decimal Length: {$length}\n";
        
        $text = "";
        for ($i = 0; $i < $length; $i++) {
            $c_bits = $this->read_bits(8);
            $c = bindec($c_bits);
            $character = chr($c);

            $binary_value_after_extraction = decbin(imagecolorat($this->image, $this->cur_x, $this->cur_y));
            if ($i < 2 || $i >= $length - 2) {
                echo "\nExtraction Step: " . ($i + 1) . "\n";
                echo "=================================\n";
                echo "Current Pointer Position: {$this->cur_x}, {$this->cur_y}, {$this->cur_channel}\n";
                echo "Extracted Bit: {$binary_value_after_extraction[-1]}\n";
                echo "Pixel Value After Extraction: " . bindec($binary_value_after_extraction) . "\n";
                echo "Binary Value After Extraction: {$binary_value_after_extraction}\n";
                echo "Binary Character Bits: {$c_bits}\n";
                echo "Decimal Character: {$c}\n";
                echo "Character: {$character}\n";
            }

            $text .= $character;
        }

        echo "\nCipherText Hasil Extract : {$text}\n";
        return $text;
    }

    public function save($dstPath) {
        imagepng($this->image, $dstPath);
        imagedestroy($this->image);
    }
}

if (php_sapi_name() == 'cli') {
    $lsb = new LSB('UTY.png');
    $lsb->embed("This is a secret message.");
    $lsb->save('embedded_image.png');

    $lsbExtract = new LSB('embedded_image.png');
    $text = $lsbExtract->extract();
    echo $text;
}
?>
