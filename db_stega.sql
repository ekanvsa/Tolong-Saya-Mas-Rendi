-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Jun 2024 pada 13.21
-- Versi server: 10.4.24-MariaDB
-- Versi PHP: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_stega`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `huffmancode`
--

CREATE TABLE `huffmancode` (
  `huffmanId` int(11) NOT NULL,
  `messageId` int(11) NOT NULL,
  `huffmanTable` text NOT NULL,
  `encodedMessage` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `image`
--

CREATE TABLE `image` (
  `imageId` int(11) NOT NULL,
  `fileName` varchar(225) NOT NULL,
  `filePath` varchar(225) NOT NULL,
  `fileSize` int(11) NOT NULL,
  `fileType` varchar(50) NOT NULL,
  `userId` int(11) NOT NULL,
  `dateUploaded` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `lsb_decode`
--

CREATE TABLE `lsb_decode` (
  `decodeId` int(11) NOT NULL,
  `imageId` int(11) NOT NULL,
  `messageId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `decodingDate` datetime NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `lsb_encoding`
--

CREATE TABLE `lsb_encoding` (
  `encodingId` int(11) NOT NULL,
  `imageId` int(11) NOT NULL,
  `messageId` int(11) NOT NULL,
  `encodedImagePath` varchar(255) NOT NULL,
  `encodingDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `message`
--

CREATE TABLE `message` (
  `messageId` int(11) NOT NULL,
  `content` text NOT NULL,
  `length` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `dateEncoded` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `userId` int(11) NOT NULL,
  `password` varchar(225) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`userId`, `password`, `email`) VALUES
(5, '$2y$10$IK3mBEvSpPPhvOvvLA9uCOjjSXXxUdHR9imJTBXMU2.wxEN2b1Uli', 'eka@gmail.com'),
(6, '$2y$10$sd.MBQxAWKish9e0WBDM3edfGBe5EUettQY1DmM8hqDKRqHChDGki', 'novita@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `huffmancode`
--
ALTER TABLE `huffmancode`
  ADD PRIMARY KEY (`huffmanId`),
  ADD KEY `messageId` (`messageId`);

--
-- Indeks untuk tabel `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`imageId`),
  ADD KEY `userId` (`userId`);

--
-- Indeks untuk tabel `lsb_decode`
--
ALTER TABLE `lsb_decode`
  ADD PRIMARY KEY (`decodeId`),
  ADD KEY `imageId` (`imageId`),
  ADD KEY `messageId` (`messageId`),
  ADD KEY `userId` (`userId`);

--
-- Indeks untuk tabel `lsb_encoding`
--
ALTER TABLE `lsb_encoding`
  ADD PRIMARY KEY (`encodingId`),
  ADD KEY `imageId` (`imageId`),
  ADD KEY `lsb_encoding_ibfk_2` (`messageId`);

--
-- Indeks untuk tabel `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`messageId`),
  ADD KEY `userId` (`userId`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `huffmancode`
--
ALTER TABLE `huffmancode`
  MODIFY `huffmanId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `image`
--
ALTER TABLE `image`
  MODIFY `imageId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `lsb_decode`
--
ALTER TABLE `lsb_decode`
  MODIFY `decodeId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `lsb_encoding`
--
ALTER TABLE `lsb_encoding`
  MODIFY `encodingId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `message`
--
ALTER TABLE `message`
  MODIFY `messageId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `huffmancode`
--
ALTER TABLE `huffmancode`
  ADD CONSTRAINT `huffmancode_ibfk_1` FOREIGN KEY (`messageId`) REFERENCES `message` (`messageId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `image_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `lsb_decode`
--
ALTER TABLE `lsb_decode`
  ADD CONSTRAINT `lsb_decode_ibfk_1` FOREIGN KEY (`imageId`) REFERENCES `image` (`imageId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lsb_decode_ibfk_2` FOREIGN KEY (`messageId`) REFERENCES `message` (`messageId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lsb_decode_ibfk_3` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `lsb_encoding`
--
ALTER TABLE `lsb_encoding`
  ADD CONSTRAINT `lsb_encoding_ibfk_1` FOREIGN KEY (`imageId`) REFERENCES `image` (`imageId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lsb_encoding_ibfk_2` FOREIGN KEY (`messageId`) REFERENCES `message` (`messageId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
