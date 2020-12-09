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

CREATE TABLE usuaris(
	user_id number(6),
	nom varchar2(32) not null,
	cognom1 varchar2(32) not null,
	cognom2 varchar2(32) not null,
	email varchar2(255) not null,
	
	CONSTRAINT PK_USUARIS PRIMARY KEY (user_id)
);

CREATE TABLE viatges(
    viatge_id number(6) not null,
    nom varchar2(32) not null,
	descripcio varchar2(255) not null,
	
    data_creacio date not null,
    ultima_modificacio date not null,
	
	CONSTRAINT PK_VIATGES PRIMARY KEY (viatge_id)
);

CREATE TABLE usuaris_viatges(
	user_id number(6) not null,
	viatge_id number(6) not null,
	data_unio date not null,
	
	CONSTRAINT FK_USER_ID_UV FOREIGN KEY (user_id) REFERENCES usuaris(user_id),
	CONSTRAINT FK_VIATGE_ID_UV FOREIGN KEY (viatge_id) REFERENCES viatges(viatge_id),
	CONSTRAINT PK_USUARIS_VIATGES PRIMARY KEY (user_id,viatge_id)
);

CREATE TABLE despeses(
    despesa_id number(6) not null,
	viatge_id number(6) not null,
	user_id number(6) not null,
	concepte varchar2(32) not null,
	quantitat number(6) not null,
	
	CONSTRAINT PK_DESPESES PRIMARY KEY (despesa_id),
	CONSTRAINT FK_VIATGE_ID_D FOREIGN KEY (viatge_id) REFERENCES viatges(viatge_id),
	CONSTRAINT FK_USER_ID_D FOREIGN KEY (user_id) REFERENCES usuaris(user_id)
);

CREATE TABLE invitacions(
	invitation_id number(6) not null,
	user_id number(6) not null,
	email varchar2(255) not null,
	data_invitacio date not null,
	
	CONSTRAINT PK_INVITACIONS PRIMARY KEY (invitation_id),
	CONSTRAINT FK_USER_ID_I FOREIGN KEY (user_id) REFERENCES usuaris(user_id),
	CONSTRAINT FK_EMAIL_I FOREIGN KEY (email) REFERENCES usuaris(email)
);