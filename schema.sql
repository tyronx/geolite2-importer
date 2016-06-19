--
-- Table structure for table `country`
--

drop table `country`;
CREATE TABLE `country` (
  `geoname_id` int(11) NOT NULL,
  `locale_code` varchar(3) NOT NULL,
  `continent_code` varchar(50) NOT NULL,
  `continent_name` varchar(50) NOT NULL,
  `country_iso_code` varchar(3) NOT NULL,
  `country_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `countryblocksip4`
--

drop table `countryblocksip4`;
CREATE TABLE `countryblocksip4` (
  `network` char(100) DEFAULT NULL,
  `geoname_id` int(11) DEFAULT NULL,
  `registered_country_geoname_id` int(11) DEFAULT NULL,
  `represented_country_geoname_id` int(11) DEFAULT NULL,
  `is_anonymous_proxy` int(11) DEFAULT NULL,
  `is_satellite_provider` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
