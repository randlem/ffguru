CREATE TABLE players (
	id INTEGER PRIMARY KEY,
	name TEXT,
	position TEXT,
	bye INTEGER,
	team TEXT
);

CREATE TABLE pricing (
	id INTEGER PRIMARY KEY,
	projected INTEGER,
	skew INTEGER,
	paid INTEGER,
	inflated INTEGER
);
