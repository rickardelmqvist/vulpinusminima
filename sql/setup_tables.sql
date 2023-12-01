CREATE TABLE t_user (
    userId INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
,   email VARCHAR(100) NOT NULL
,   name  VARCHAR(100) NOT NULL
,   isVerified BOOLEAN DEFAULT FALSE
,   verifiedDate TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
,   createdDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE t_password (
    userId INT NOT NULL
,   md5Value VARCHAR(100)
,   createdDate  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE t_login (
    userId INT NOT NULL
,   passKey INT NOT NULL
,   authenticated BOOLEAN DEFAULT FALSE
,   sessionid VARCHAR(50) NOT NULL
,   lastChange  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE t_event (
    eventId INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
,   eventName VARCHAR(50) NOT NULL
,   openForRegister BOOLEAN DEFAULT FALSE
,   eventTimestamp TIMESTAMP NOT NULL
,   eventLocation VARCHAR(100)
,   lastChange  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO t_event (
	eventName
,	openForRegister
,	eventDate
,	eventTime
,	eventLocation
)
VALUES (
	"Minirävens Dag"
,	TRUE
,	"2020-03-06 14:00:00"
,	"Drottningtorget, Malmö"
);

CREATE TABLE t_attend (
    userId INT NOT NULL
,   eventId INT NOT NULL
,   hasAnswered BOOLEAN DEFAULT FALSE
,   answer BOOLEAN DEFAULT FALSE
,   didAttend BOOLEAN DEFAULT FALSE
,   lastChange  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO t_attend (
	userId
,	eventId
)
VALUES (
	1
,	5
);

SELECT 
    t_event.eventId
,	t_attend.userId
,	t_event.eventName
,	t_event.eventDate
,	t_event.eventTime
,	t_event.eventLocation
,   t_attend.hasAnswered
,   t_attend.answer
,   t_attend.didAttend
FROM 
    t_event 
LEFT JOIN
    t_attend
ON
    t_event.eventId = t_attend.eventId
WHERE t_attend.userId = 1 AND t_event.eventId = 5