/*
usuaris
user_id, nom, cognom1, cognom2, email

usuaris_viatges

viatges
viatge_id, nom, description, main_currency, data_creacio, last_modification

despeses
despesa_id, viatge_id, user_id, concepte, quantitat

invitacions
invitation_id, user_id, email

amistats
clave compuesta con user1_id y user2_id
user1_id, user2_id, friendship_start_date

*/

DROP DATABASE IF EXISTS tripcount;
CREATE DATABASE tripcount;

USE tripcount;

CREATE TABLE users(
	user_id int not null AUTO_INCREMENT,
	username varchar(32) not null,
	name varchar(32) not null,
	lastname1 varchar(32) not null,
	lastname2 varchar(32) not null,
	email varchar(255) not null,
	password varchar(255) not null,
	
	CONSTRAINT PK_USERS PRIMARY KEY (user_id),
	CONSTRAINT UC_EMAIL UNIQUE (email) 
);

CREATE TABLE friendships(
	user1_id int not null,
	user2_id int not null,
	friendship_start_date timestamp not null,

	CONSTRAINT FK_USER1_ID FOREIGN KEY (user1_id) REFERENCES users(user_id),
	CONSTRAINT FK_USER2_ID FOREIGN KEY (user2_id) REFERENCES users(user_id),
	CONSTRAINT PK_FRIENDSHIPS PRIMARY KEY (user1_id, user2_id)
);

CREATE TABLE currencies(
	currency_iso char(3) not null,
	name varchar(255) not null,
	currency_char varchar(8) not null,

	CONSTRAINT PK_CURRENCIES PRIMARY KEY (currency_iso),
	CONSTRAINT UC_NAME UNIQUE (name)
);

INSERT INTO `currencies` (`currency_iso`, `name`, `currency_char`) VALUES
('ADP', 'Andorran Peseta', '₧'),
('AED', 'United Arab Emirates Dirham', 'د.إ'),
('AFA', 'Afghanistan Afghani', '؋'),
('ALL', 'Albanian Lek', '	L'),
('AMD', 'Armenian Dram', '֏'),
('ANG', 'Netherlands Antillian Guilder', 'NAƒ'),
('AOK', 'Angolan Kwanza', 'Kz'),
('ARS', 'Argentine Peso', '$'),
('AUD', 'Australian Dollar', '$'),
('AWG', 'Aruban Florin', 'ƒ'),
('BBD', 'Barbados Dollar', 'Bds$'),
('BDT', 'Bangladeshi Taka', '৳'),
('BGN', 'Bulgarian Lev', 'лв'),
('BHD', 'Bahraini Dinar', '.د.ب'),
('BIF', 'Burundi Franc', 'FBu'),
('BMD', 'Bermudian Dollar', '$'),
('BND', 'Brunei Dollar', 'B$'),
('BOB', 'Bolivian Boliviano', 'Bs'),
('BRL', 'Brazilian Real', '	R$'),
('BSD', 'Bahamian Dollar', 'B$'),
('BTN', 'Bhutan Ngultrum', 'Nu.'),
('BUK', 'Burma Kyat', 'K'),
('BWP', 'Botswanian Pula', 'P'),
('BZD', 'Belize Dollar', 'BZ$'),
('CAD', 'Canadian Dollar', 'CA$'),
('CHF', 'Swiss Franc', 'CHF'),
('CLF', 'Chilean Unidades de Fomento', 'UF'),
('CLP', 'Chilean Peso', '$'),
('CNY', 'Yuan (Chinese) Renminbi', '元'),
('COP', 'Colombian Peso', 'COL$'),
('CRC', 'Costa Rican Colon', '₡'),
('CUP', 'Cuban Peso', '₱'),
('CVE', 'Cape Verde Escudo', '$'),
('CYP', 'Cyprus Pound', '£'),
('CZK', 'Czech Republic Koruna', 'Kč'),
('DKK', 'Danish Krone', 'kr.'),
('DOP', 'Dominican Peso', 'RD$'),
('DZD', 'Algerian Dinar', 'دج'),
('ECS', 'Ecuador Sucre', 'S/.'),
('EEK', 'Estonian Kroon (EEK)', 'kr'),
('EGP', 'Egyptian Pound', 'E£'),
('ETB', 'Ethiopian Birr', 'ብር'),
('EUR', 'Euro', '€'),
('FJD', 'Fiji Dollar', 'FJ$'),
('FKP', 'Falkland Islands Pound', 'FK£'),
('GBP', 'British Pound', '£'),
('GHC', 'Ghanaian Cedi', 'GH₵'),
('GIP', 'Gibraltar Pound', '£'),
('GMD', 'Gambian Dalasi', 'D'),
('GNF', 'Guinea Franc', 'FG'),
('GTQ', 'Guatemalan Quetzal', 'Q'),
('GWP', 'Guinea-Bissau Peso', 'GWP'),
('GYD', 'Guyanan Dollar', 'GY$'),
('HKD', 'Hong Kong Dollar', 'HK$'),
('HNL', 'Honduran Lempira', 'L'),
('HTG', 'Haitian Gourde', 'G'),
('HUF', 'Hungarian Forint', 'Ft'),
('IDR', 'Indonesian Rupiah', 'Rp'),
('IEP', 'Irish Punt', '£'),
('ILS', 'Israeli Shekel', '₪'),
('INR', 'Indian Rupee', '₹'),
('IQD', 'Iraqi Dinar', 'د.ع'),
('IRR', 'Iranian Rial', '﷼'),
('JMD', 'Jamaican Dollar', 'J$'),
('JOD', 'Jordanian Dinar', 'د.أ'),
('JPY', 'Japanese Yen', '¥'),
('KES', 'Kenyan Schilling', 'KSh'),
('KHR', 'Kampuchean (Cambodian) Riel', '៛'),
('KMF', 'Comoros Franc', 'CF'),
('KPW', 'North Korean Won', '₩'),
('KRW', '(South) Korean Won', '	₩'),
('KWD', 'Kuwaiti Dinar', 'د.ك '),
('KYD', 'Cayman Islands Dollar', 'CI$'),
('LAK', 'Lao Kip', '₭'),
('LBP', 'Lebanese Pound', 'ل.ل.'),
('LKR', 'Sri Lanka Rupee', 'රු'),
('LRD', 'Liberian Dollar', 'LD$'),
('LSL', 'Lesotho Loti', 'L'),
('LYD', 'Libyan Dinar', 'ل.د'),
('MAD', 'Moroccan Dirham', 'DH'),
('MGF', 'Malagasy Franc', 'MF'),
('MNT', 'Mongolian Tugrik', '₮'),
('MOP', 'Macau Pataca', 'MOP$'),
('MRO', 'Mauritanian Ouguiya', 'UM'),
('MTL', 'Maltese Lira', '₤'),
('MUR', 'Mauritius Rupee', '₨'),
('MVR', 'Maldive Rufiyaa', 'Rf'),
('MWK', 'Malawi Kwacha', 'K'),
('MXP', 'Mexican Peso', 'Mex$'),
('MYR', 'Malaysian Ringgit', 'RM'),
('MZM', 'Mozambique Metical', 'MT'),
('NAD', 'Namibian Dollar', '$'),
('NGN', 'Nigerian Naira', '₦'),
('NIO', 'Nicaraguan Cordoba', 'C$'),
('NOK', 'Norwegian Kroner', 'kr'),
('NPR', 'Nepalese Rupee', 'रु'),
('NZD', 'New Zealand Dollar', 'NZ$'),
('OMR', 'Omani Rial', 'ر.ع'),
('PAB', 'Panamanian Balboa', 'B.'),
('PEN', 'Peruvian Nuevo Sol', 'S'),
('PGK', 'Papua New Guinea Kina', 'K'),
('PHP', 'Philippine Peso', '₱'),
('PKR', 'Pakistan Rupee', '₨'),
('PLN', 'Polish Zloty', 'zł'),
('PYG', 'Paraguay Guarani', '₲'),
('QAR', 'Qatari Rial', 'ر.ق'),
('RON', 'Romanian Leu', 'L'),
('RUB', 'Russian Ruble', '₽'),
('RWF', 'Rwanda Franc', 'R₣'),
('SAR', 'Saudi Arabian Riyal', 'ر.س '),
('SBD', 'Solomon Islands Dollar', 'SI$'),
('SCR', 'Seychelles Rupee', 'SR'),
('SDP', 'Sudanese Pound', '£SD'),
('SEK', 'Swedish Krona', 'kr'),
('SGD', 'Singapore Dollar', 'S$'),
('SHP', 'St. Helena Pound', '£'),
('SKK', 'Slovak Koruna', 'Sk'),
('SLL', 'Sierra Leone Leone', 'Le'),
('SOS', 'Somali Schilling', 'Sh.So.'),
('SRG', 'Suriname Guilder', 'ƒ'),
('STD', 'Sao Tome and Principe Dobra', 'Db'),
('SVC', 'El Salvador Colon', '	₡'),
('SYP', 'Syrian Potmd', '£S'),
('SZL', 'Swaziland Lilangeni', 'L'),
('THB', 'Thai Baht', '฿'),
('TND', 'Tunisian Dinar', 'د.ت'),
('TOP', 'Tongan Paanga', 'T$'),
('TPE', 'East Timor Escudo', 'TPE'),
('TRY', 'Turkish Lira', '₺'),
('TTD', 'Trinidad and Tobago Dollar', 'TT$'),
('TWD', 'Taiwan Dollar', '圓'),
('TZS', 'Tanzanian Schilling', 'TSh'),
('UGX', 'Uganda Shilling', 'USh'),
('USD', 'US Dollar', '$'),
('UYU', 'Uruguayan Peso', '$U'),
('VEF', 'Venezualan Bolivar', 'Bs.S'),
('VND', 'Vietnamese Dong', '₫'),
('VUV', 'Vanuatu Vatu', 'VT'),
('WST', 'Samoan Tala', 'WS$'),
('XCD', 'East Caribbean Dollar', 'EC$'),
('XPF', 'Comptoirs Français du Pacifique Francs', 'F'),
('YDD', 'Democratic Yemeni Dinar', 'YDD'),
('YER', 'Yemeni Rial', '﷼'),
('YUD', 'New Yugoslavia Dinar', 'дин'),
('ZAR', 'South African Rand', 'R'),
('ZMK', 'Zambian Kwacha', 'K'),
('ZRZ', 'Zaire Zaire', 'ZRZ'),
('ZWD', 'Zimbabwe Dollar', '$');

CREATE TABLE travels(
    travel_id int not null AUTO_INCREMENT,
    name varchar(32) not null,
	description varchar(255) not null,
	currency_iso char(3) not null,

    creation_date TIMESTAMP not null DEFAULT CURRENT_TIMESTAMP(),
    last_modification TIMESTAMP not null DEFAULT CURRENT_TIMESTAMP(),
	
	CONSTRAINT PK_TRAVELS PRIMARY KEY (travel_id),
	CONSTRAINT FK_CURRENCY_ISO FOREIGN KEY (currency_iso) REFERENCES currencies(currency_iso)
);

CREATE TABLE users_travels(
	user_id int not null,
	travel_id int not null,
	association_date TIMESTAMP not null DEFAULT CURRENT_TIMESTAMP(),
	
	CONSTRAINT FK_USER_ID_UV FOREIGN KEY (user_id) REFERENCES users(user_id),
	CONSTRAINT FK_TRAVEL_ID_UV FOREIGN KEY (travel_id) REFERENCES travels(travel_id),
	CONSTRAINT PK_USERS_TRAVELS PRIMARY KEY (user_id,travel_id)
);

CREATE TABLE expenses(
    expense_id int not null AUTO_INCREMENT,
	travel_id int not null,
	user_id int not null,
	concept varchar(32) not null,
	quantity decimal(13, 4) not null,
	
	CONSTRAINT PK_EXPENSES PRIMARY KEY (expense_id),
	CONSTRAINT FK_TRAVEL_ID_D FOREIGN KEY (travel_id) REFERENCES travels(travel_id),
	CONSTRAINT FK_USER_ID_D FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE invitations(
	invitation_id int not null AUTO_INCREMENT,
	user_id int not null,
	email varchar(255) not null,
	date_invitation TIMESTAMP not null DEFAULT CURRENT_TIMESTAMP(),
	
	CONSTRAINT PK_INVITATIONS PRIMARY KEY (invitation_id),
	CONSTRAINT FK_USER_ID_I FOREIGN KEY (user_id) REFERENCES users(user_id)
);