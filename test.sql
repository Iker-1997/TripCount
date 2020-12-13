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

	CONSTRAINT PK_CURRENCIES PRIMARY KEY (currency_iso),
	CONSTRAINT UC_NAME UNIQUE (name)
);

INSERT INTO `currencies` (`currency_iso`, `name`) VALUES
('KRW', '(South) Korean Won'),
('AFA', 'Afghanistan Afghani'),
('ALL', 'Albanian Lek'),
('DZD', 'Algerian Dinar'),
('ADP', 'Andorran Peseta'),
('AOK', 'Angolan Kwanza'),
('ARS', 'Argentine Peso'),
('AMD', 'Armenian Dram'),
('AWG', 'Aruban Florin'),
('AUD', 'Australian Dollar'),
('BSD', 'Bahamian Dollar'),
('BHD', 'Bahraini Dinar'),
('BDT', 'Bangladeshi Taka'),
('BBD', 'Barbados Dollar'),
('BZD', 'Belize Dollar'),
('BMD', 'Bermudian Dollar'),
('BTN', 'Bhutan Ngultrum'),
('BOB', 'Bolivian Boliviano'),
('BWP', 'Botswanian Pula'),
('BRL', 'Brazilian Real'),
('GBP', 'British Pound'),
('BND', 'Brunei Dollar'),
('BGN', 'Bulgarian Lev'),
('BUK', 'Burma Kyat'),
('BIF', 'Burundi Franc'),
('CAD', 'Canadian Dollar'),
('CVE', 'Cape Verde Escudo'),
('KYD', 'Cayman Islands Dollar'),
('CLP', 'Chilean Peso'),
('CLF', 'Chilean Unidades de Fomento'),
('COP', 'Colombian Peso'),
('XOF', 'Communauté Financière Africaine BCEAO - Francs'),
('XAF', 'Communauté Financière Africaine BEAC, Francs'),
('KMF', 'Comoros Franc'),
('XPF', 'Comptoirs Français du Pacifique Francs'),
('CRC', 'Costa Rican Colon'),
('CUP', 'Cuban Peso'),
('CYP', 'Cyprus Pound'),
('CZK', 'Czech Republic Koruna'),
('DKK', 'Danish Krone'),
('YDD', 'Democratic Yemeni Dinar'),
('DOP', 'Dominican Peso'),
('XCD', 'East Caribbean Dollar'),
('TPE', 'East Timor Escudo'),
('ECS', 'Ecuador Sucre'),
('EGP', 'Egyptian Pound'),
('SVC', 'El Salvador Colon'),
('EEK', 'Estonian Kroon (EEK)'),
('ETB', 'Ethiopian Birr'),
('EUR', 'Euro'),
('FKP', 'Falkland Islands Pound'),
('FJD', 'Fiji Dollar'),
('GMD', 'Gambian Dalasi'),
('GHC', 'Ghanaian Cedi'),
('GIP', 'Gibraltar Pound'),
('XAU', 'Gold, Ounces'),
('GTQ', 'Guatemalan Quetzal'),
('GNF', 'Guinea Franc'),
('GWP', 'Guinea-Bissau Peso'),
('GYD', 'Guyanan Dollar'),
('HTG', 'Haitian Gourde'),
('HNL', 'Honduran Lempira'),
('HKD', 'Hong Kong Dollar'),
('HUF', 'Hungarian Forint'),
('INR', 'Indian Rupee'),
('IDR', 'Indonesian Rupiah'),
('XDR', 'International Monetary Fund (IMF) Special Drawing Rights'),
('IRR', 'Iranian Rial'),
('IQD', 'Iraqi Dinar'),
('IEP', 'Irish Punt'),
('ILS', 'Israeli Shekel'),
('JMD', 'Jamaican Dollar'),
('JPY', 'Japanese Yen'),
('JOD', 'Jordanian Dinar'),
('KHR', 'Kampuchean (Cambodian) Riel'),
('KES', 'Kenyan Schilling'),
('KWD', 'Kuwaiti Dinar'),
('LAK', 'Lao Kip'),
('LBP', 'Lebanese Pound'),
('LSL', 'Lesotho Loti'),
('LRD', 'Liberian Dollar'),
('LYD', 'Libyan Dinar'),
('MOP', 'Macau Pataca'),
('MGF', 'Malagasy Franc'),
('MWK', 'Malawi Kwacha'),
('MYR', 'Malaysian Ringgit'),
('MVR', 'Maldive Rufiyaa'),
('MTL', 'Maltese Lira'),
('MRO', 'Mauritanian Ouguiya'),
('MUR', 'Mauritius Rupee'),
('MXP', 'Mexican Peso'),
('MNT', 'Mongolian Tugrik'),
('MAD', 'Moroccan Dirham'),
('MZM', 'Mozambique Metical'),
('NAD', 'Namibian Dollar'),
('NPR', 'Nepalese Rupee'),
('ANG', 'Netherlands Antillian Guilder'),
('YUD', 'New Yugoslavia Dinar'),
('NZD', 'New Zealand Dollar'),
('NIO', 'Nicaraguan Cordoba'),
('NGN', 'Nigerian Naira'),
('KPW', 'North Korean Won'),
('NOK', 'Norwegian Kroner'),
('OMR', 'Omani Rial'),
('PKR', 'Pakistan Rupee'),
('XPD', 'Palladium Ounces'),
('PAB', 'Panamanian Balboa'),
('PGK', 'Papua New Guinea Kina'),
('PYG', 'Paraguay Guarani'),
('PEN', 'Peruvian Nuevo Sol'),
('PHP', 'Philippine Peso'),
('XPT', 'Platinum, Ounces'),
('PLN', 'Polish Zloty'),
('QAR', 'Qatari Rial'),
('RON', 'Romanian Leu'),
('RUB', 'Russian Ruble'),
('RWF', 'Rwanda Franc'),
('WST', 'Samoan Tala'),
('STD', 'Sao Tome and Principe Dobra'),
('SAR', 'Saudi Arabian Riyal'),
('SCR', 'Seychelles Rupee'),
('SLL', 'Sierra Leone Leone'),
('XAG', 'Silver, Ounces'),
('SGD', 'Singapore Dollar'),
('SKK', 'Slovak Koruna'),
('SBD', 'Solomon Islands Dollar'),
('SOS', 'Somali Schilling'),
('ZAR', 'South African Rand'),
('LKR', 'Sri Lanka Rupee'),
('SHP', 'St. Helena Pound'),
('SDP', 'Sudanese Pound'),
('SRG', 'Suriname Guilder'),
('SZL', 'Swaziland Lilangeni'),
('SEK', 'Swedish Krona'),
('CHF', 'Swiss Franc'),
('SYP', 'Syrian Potmd'),
('TWD', 'Taiwan Dollar'),
('TZS', 'Tanzanian Schilling'),
('THB', 'Thai Baht'),
('TOP', 'Tongan Paanga'),
('TTD', 'Trinidad and Tobago Dollar'),
('TND', 'Tunisian Dinar'),
('TRY', 'Turkish Lira'),
('UGX', 'Uganda Shilling'),
('AED', 'United Arab Emirates Dirham'),
('UYU', 'Uruguayan Peso'),
('USD', 'US Dollar'),
('VUV', 'Vanuatu Vatu'),
('VEF', 'Venezualan Bolivar'),
('VND', 'Vietnamese Dong'),
('YER', 'Yemeni Rial'),
('CNY', 'Yuan (Chinese) Renminbi'),
('ZRZ', 'Zaire Zaire'),
('ZMK', 'Zambian Kwacha'),
('ZWD', 'Zimbabwe Dollar');

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