CREATE TABLE `user` (
  `personId` varchar(36) NOT NULL,
  `faceId` varchar(36) NOT NULL,
  `name` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `user`
  ADD PRIMARY KEY (`personId`),
  ADD UNIQUE KEY `faceId` (`faceId`),
  ADD UNIQUE KEY `email` (`email`);
