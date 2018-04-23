--create database
CREATE DATABASE hospital WITH OWNER = postgres;

--connect to database
\c hospital

--sequences
CREATE SEQUENCE seqEmpID
    INCREMENT BY 1
        START WITH 1 NO CYCLE
;

CREATE SEQUENCE seqPatientID
    INCREMENT BY 1
        START WITH 1 NO CYCLE
;

CREATE SEQUENCE seqCtID
    INCREMENT BY 1
        START WITH 1 NO CYCLE
;

CREATE SEQUENCE seqCaseID
    INCREMENT BY 1
        START WITH 1 NO CYCLE
;

CREATE SEQUENCE seqDiagID
    INCREMENT BY 1
        START WITH 1 NO CYCLE
;

CREATE SEQUENCE seqPreID
    INCREMENT BY 1
        START WITH 1 NO CYCLE
;

CREATE SEQUENCE seqLabID
    INCREMENT BY 1
        START WITH 1 NO CYCLE
;

--tables
CREATE TABLE Employee (
    eID SMALLINT NOT NULL DEFAULT nextval('seqEmpID'),
    SSN BIGINT NOT NULL UNIQUE,
    fname VARCHAR(20) NOT NULL,
    mname VARCHAR(20) NOT NULL,
    lname VARCHAR(25) NOT NULL,
    street VARCHAR(25) NOT NULL,
    city VARCHAR(25) NOT NULL,
    state VARCHAR(2) NOT NULL,
    zip INTEGER NOT NULL,
    phone bigint NOT NULL,
    licType VARCHAR(20) NOT NULL,
    licNo bigint NOT NULL UNIQUE,
    sDate DATE NOT NULL,
    eDate DATE,
    PRIMARY KEY (eID)
) ;

CREATE TABLE Patient (
    pID SMALLINT NOT NULL DEFAULT nextval('seqPatientID'),
    SSN BIGINT NOT NULL UNIQUE,
    fname VARCHAR(20) NOT NULL,
    mname VARCHAR(20) NOT NULL,
    lname VARCHAR(25) NOT NULL,
    street VARCHAR(25) NOT NULL,
    city VARCHAR(25) NOT NULL,
    state VARCHAR(2) NOT NULL,
    zip INTEGER NOT NULL,
    phone bigint NOT NULL,
    dob DATE NOT NULL,
    sex VARCHAR(8) NOT NULL,
    insType VARCHAR(30) NOT NULL,
    language VARCHAR(20) NOT NULL,
    PRIMARY KEY (pID)
) ;
    
CREATE TABLE CareTeam (
    careteamID SMALLINT NOT NULL DEFAULT nextval('seqCtID'),
    dept VARCHAR(30) NOT NULL,
    PRIMARY KEY (careteamID)
) ;

CREATE TABLE Prescription (
    preID SMALLINT NOT NULL DEFAULT nextval('seqPreID'),
    medication VARCHAR(50) NOT NULL,
    dosage FLOAT NOT NULL,
    frequency VARCHAR(100) NOT NULL,
    PRIMARY KEY (preID)
) ;

CREATE TABLE Labs (
    labID SMALLINT NOT NULL DEFAULT nextval('seqLabID'),
    BUN INTEGER NOT NULL,
    calcium FLOAT NOT NULL,
    c02 INTEGER NOT NULL,
    chloride INTEGER NOT NULL,
    creatinine FLOAT NOT NULL,
    glucose INTEGER NOT NULL,
    potassium FLOAT NOT NULL,
    sodium INTEGER NOT NULL,
    PRIMARY KEY (labID)
) ;
    
CREATE TABLE Diagnosis (
    dID SMALLINT NOT NULL DEFAULT nextval('seqDiagID'),
    diagnosis VARCHAR(200) NOT NULL,
    status INTEGER NOT NULL,
    lID SMALLINT NOT NULL REFERENCES Labs (labID),
    eID SMALLINT NOT NULL REFERENCES employee (eID),
    PRIMARY KEY (dID)
) ;
    
CREATE TABLE cases (
    cID SMALLINT NOT NULL DEFAULT nextval('seqCaseID'),
    bpSys INTEGER,
    bpDia INTEGER,
    hRate INTEGER,
    rRate INTEGER,
    vDate DATE NOT NULL,
    pID SMALLINT NOT NULL REFERENCES patient(pID),
    dID SMALLINT NOT NULL UNIQUE REFERENCES diagnosis(dID),
    eID SMALLINT NOT NULL REFERENCES employee(eID),
    PRIMARY KEY (cID)
) ;

CREATE TABLE Forms (
    eID SMALLINT REFERENCES employee(eID),
    careteamID SMALLINT REFERENCES careteam(careteamID),
    sDate DATE NOT NULL
) ;

CREATE TABLE Prescribes (
    prID SMALLINT UNIQUE REFERENCES prescription(preID),
    dID SMALLINT REFERENCES diagnosis(dID),
    sDate DATE NOT NULL,
    eDate DATE NOT NULL
) ;

CREATE TABLE Seenby (
    pID SMALLINT REFERENCES patient(pID),
    careteamID SMALLINT REFERENCES careteam(careteamID),
    date DATE NOT NULL
) ;

--indexes
CREATE INDEX idx_employee_id
    ON employee(eid)
;

CREATE INDEX idx_employee_ssn
    ON employee(ssn)
;


CREATE INDEX idx_employee_licno
    ON employee(licNo)
;


CREATE INDEX idx_cases_id
    ON cases(cID)
;


CREATE INDEX idx_patient_id
    ON patient(pID)
;


CREATE INDEX idx_patient_ssn
    ON patient(ssn)
;


CREATE INDEX idx_patient_lname
    ON patient(lname)
;


CREATE INDEX idx_diagnosis_id
    ON diagnosis(dID)
;


CREATE INDEX idx_prescription_id
    ON prescription(preID);
;


CREATE INDEX idx_lab_id
    ON labs(labID)
;

--views
--query 1 DONE
--List all patients who were at the hospital in 1999 but did not get prescribed any medication
CREATE OR REPLACE VIEW query1 AS
SELECT DISTINCT p.pID, fname, lname
    FROM patient p WHERE EXISTS (SELECT * FROM diagnosis d, cases c
WHERE p.pID = c.pID AND c.vDate <= '12/31/1999'::date AND c.vDate >= '1999-01-01'::date AND d.did = c.did AND NOT EXISTS (SELECT * FROM prescribes pr WHERE pr.dID = d.dID))
;

--query 2 DONE
--List all nurses who have seen at least 3 cases but no longer work at the hospital
CREATE OR REPLACE VIEW query2 AS
        SELECT DISTINCT e.eID, fname, lname
        FROM employee e, cases c1, cases c2, cases c3
        WHERE e.eDate IS NOT NULL
        AND e.eID = c1.eID
        AND e.eID = c2.eID
        AND e.eID = c3.eID
        AND c1.cID != c2.cID
        AND c2.cID != c3.cID
        AND c1.cID != c3.cID
;

--query 3 DONE
--List the patient with the lowest heart rate recorded in the hospital
CREATE OR REPLACE VIEW query3 AS
  SELECT DISTINCT p.pID, fname, lname
    FROM patient p WHERE EXISTS (SELECT * FROM cases c WHERE
    c.pID = p.pID AND NOT EXISTS (SELECT * FROM cases c2 WHERE
    c2.cID != c.cID AND c2.hrate < c.hrate))
;

--query 4 DONE
--List the youngest patient who has been to the hospital
CREATE OR REPLACE VIEW query4 AS
SELECT DISTINCT p.pid, fname, lname, p.dob
    From Patient p WHERE NOT EXISTS(SELECT * FROM patient p2 WHERE
    p.pID != p2.pID AND p.dob < p2.dob)
;

--query 5 DONE
--List all patients with 2 or more current prescriptions in a single diagnosis.
CREATE OR REPLACE VIEW query5 AS
    SELECT DISTINCT  p.pid, fname, lname
    FROM patient p, cases c, diagnosis d, prescribes pr, prescribes pr2
    WHERE c.did = d.did AND p.pid = c.pid AND pr.did = d.did AND pr.eDATE >= now()
    AND pr2.did = d.did AND pr2.prid != pr.prid AND pr2.edate >= now()
;

--query6 DONE
--List all patients who have been to the hospital exactly once
CREATE OR REPLACE VIEW query6 AS
  SELECT DISTINCT p.pID, fname, lname
        FROM patient p
        WHERE EXISTS (SELECT * FROM cases c
                WHERE c.pID = p.pID AND
                NOT EXISTS (SELECT * FROM cases c2
                        WHERE c2.pID = p.pID AND c.vDate != c2.vDate
                )
        )
;

--query7 WIP
--List all patients who have received a diagnosis by every doctor currently employed at the hospital
CREATE OR REPLACE VIEW query7 AS
        SELECT DISTINCT p.pID, fname, lname
        FROM patient p
        WHERE NOT EXISTS (SELECT * FROM employee e
        WHERE e.eDate IS NULL AND e.licType = 'MD'
                    AND NOT EXISTS ( SELECT * 
FROM cases c, diagnosis d
                            WHERE p.pID = c.pID
                            AND c.dID = d.dID
            AND e.eID = d.eID
        ))
;

--query8 DONE
--List all patients who have been seen by a doctor or nurse but do not have any active prescriptions
CREATE OR REPLACE VIEW query8 AS
        SELECT DISTINCT p.pID, fname, lname
        FROM patient p
        WHERE EXISTS (SELECT * FROM cases c, diagnosis d
                WHERE c.pID = p.pID AND d.status=2 AND c.dID = d.dID
                AND NOT EXISTS (SELECT * FROM prescribes pr
                        WHERE pr.sDate <= now() ANd pr.eDate >= now()
                        AND pr.dID = d.dID
                )
        )
;

--query9 DONE
--List all male patients over the age of 50 with calcium levels over 9.0 (very high)
CREATE OR REPLACE VIEW query9 AS
        SELECT DISTINCT p.pID, fname, lname, calcium
        FROM patient p, cases c, diagnosis d, labs l
        WHERE p.sex = 'Male' AND p.dob < now()-interval'50 years'
        AND p.pID = c.pID AND c.dID = d.dID
        AND d.lID = l.labID AND l.calcium > 9.0
;

--query10 DONE
--List all patients who were only seen by a nurse but not a doctor.
CREATE OR REPLACE VIEW query10 AS
SELECT DISTINCT p.pid, fname, lname
    FROM patient p, cases c, diagnosis d WHERE
    p.pid = c.pid AND c.did = d.did AND d.status = 1
;

--query11
--List all patients and how many cases (or number of visits) they have had at the hospital
CREATE OR REPLACE VIEW query11 AS
    SELECT p.fname "First Name", p.lname "Last Name", count(*) "Number of Visits"
    FROM patient p  NATURAL JOIN cases c
    GROUP BY p.fname, p.lname
    ORDER BY count(*) DESC;
;

--query12
--List the patients names heart rates that are greater than the average heart rate of all patients
CREATE OR REPLACE VIEW query12 AS
    SELECT p.fname, p.lname, hrate
    FROM patient p NATURAL JOIN cases c
    WHERE hrate > (SELECT avg(hrate) FROM cases)
    ORDER by hrate
;

--query13
--List all nurses and how many cases they have taken.
CREATE OR REPLACE VIEW query13 AS
    SELECT e.eid, e.fname, e.lname, count(c)
    FROM employee e FULL OUTER JOIN cases c ON e.eid = c.eid
    WHERE licTYPE = 'RN'
    GROUP BY e.eid, e.fname, e.lname
    ORDER by count(c) ASC
;



--insert
--care team
INSERT INTO careteam (dept) VALUES ('Orthopedics');
INSERT INTO careteam (dept) VALUES ('Family Practice');
INSERT INTO careteam (dept) VALUES ('Trauma');
INSERT INTO careteam (dept) VALUES ('Psychiatry');
INSERT INTO careteam (dept) VALUES ('Infectious Disease');
INSERT INTO careteam (dept) VALUES ('Psychiatry');
INSERT INTO careteam (dept) VALUES ('General Surgery');
INSERT INTO careteam (dept) VALUES ('Family Practice');
INSERT INTO careteam (dept) VALUES ('Hemotology/Oncology');
INSERT INTO careteam (dept) VALUES ('Internal Medicine');
INSERT INTO careteam (dept) VALUES ('General Surgery');
INSERT INTO careteam (dept) VALUES ('Trauma');
INSERT INTO careteam (dept) VALUES ('Orthopedics');
INSERT INTO careteam (dept) VALUES ('General Surgery');
INSERT INTO careteam (dept) VALUES ('Hemotology/Oncology');
INSERT INTO careteam (dept) VALUES ('Trauma');

--insert
--employee
INSERT INTO employee (ssn, fname, mname, lname, street, city, state, zip, phone, lictype, licno, sdate, edate)
VALUES (517096320, 'Debor', 'Faust', 'Monckman', '3 Kings Circle', 'Bakersfield', 'CA', 93311, 6618643851, 'RN', 564847443, '1994-12-20', '2003-10-02');

INSERT INTO employee (ssn, fname, mname, lname, street, city, state, zip, phone, lictype, licno, sdate, eDate)
VALUES (193784642, 'Chariot', 'Gapper', 'Bohlje', '4 Bluestem Center', 'Bakersfield', 'CA', 93305, 6617425565, 'MD', 825611865, '1996-08-23', '2017-10-23');

INSERT INTO employee (ssn, fname, mname, lname, street, city, state, zip, phone, lictype, licno, sdate)
VALUES (120280133, 'Chloette', 'Abells', 'Bidewell', '54909 Pawling Avenue', 'Bakersfield', 'CA', 93304, 6616634220, 'RN', 289668898, '1993-02-26');

INSERT INTO employee (ssn, fname, mname, lname, street, city, state, zip, phone, lictype, licno, sdate, edate)
VALUES (414166762, 'Sissie', 'Saffell', 'Redon', '2128 Canary Parkway', 'Bakersfield', 'CA', 93304, 6619093340, 'MD', 670453047, '2010-06-15', '2017-10-24');

INSERT INTO employee (ssn, fname, mname, lname, street, city, state, zip, phone, lictype, licno, sdate, edate)
VALUES (505848747, 'Dyane', 'MacPhaden', 'Deavin', '33844 Welch Way', 'Bakersfield', 'CA', 93305, 6615784322, 'RN', 982229440, '2000-06-20', '2011-10-22');

INSERT INTO employee (ssn, fname, mname, lname, street, city, state, zip, phone, lictype, licno, sdate)
VALUES (281277974, 'Scotti', 'Weth', 'Lyddyard', '17376 Loftsgordon Park', 'Bakersfield', 'CA', 93305, 6618820445, 'RN', 419016262, '2012-03-20');

INSERT INTO employee (ssn, fname, mname, lname, street, city, state, zip, phone, lictype, licno, sdate, edate)
VALUES (506321656, 'Lissie', 'Finneran', 'Gilliard', '94112 Gale Alley', 'Bakersfield', 'CA', 93308, 6618843342, 'RD', 564895443, '2001-12-20', '2003-10-02');

INSERT INTO employee (ssn, fname, mname, lname, street, city, state, zip, phone, lictype, licno, sdate)
VALUES (669368746, 'Trudi', 'Dubois', 'Appleton', '9087 Havey Junction', 'Bakersfield', 'CA', 93305, 6613939952, 'RN', 017847023, '1994-12-04');

INSERT INTO employee (ssn, fname, mname, lname, street, city, state, zip, phone, lictype, licno, sdate)
VALUES (704556035, 'Stefa', 'Winckle', 'Donaho', '48 Superior Parkway', 'Bakersfield', 'CA', 93309, 6613323504, 'MD', 359148140, '2004-11-01');

INSERT INTO employee (ssn, fname, mname, lname, street, city, state, zip, phone, lictype, licno, sdate)
VALUES (223458968, 'John', 'Michael', 'Belmont', '705 Washington Avenue', 'Bakersfield', 'CA', 93308, 6615429984, 'MD', 441789454, '1998-03-07');

INSERT INTO employee (ssn, fname, mname, lname, street, city, state, zip, phone, lictype, licno, sdate)
VALUES (765878765, 'Megan', 'Lee', 'Andes', '674 Alturas Drive', 'Bakersfield', 'CA', 93305, 6612220899, 'MD', 933243543, '2006-01-15');

INSERT INTO employee (ssn, fname, mname, lname, street, city, state, zip, phone, lictype, licno, sdate)
VALUES (102348733, 'Sarah', 'Dean', 'Wiche', '322 Calloway Drive', 'Bakersfield', 'CA', 93312, 6617040056, 'RD', 988034430, '2004-07-21');

INSERT INTO employee (ssn, fname, mname, lname, street, city, state, zip, phone, lictype, licno, sdate, edate)
VALUES (934002236, 'Lei', 'Wing', 'Ding', '224 Vultee Street', 'Shafter', 'CA', 93424, 6615882283, 'MD', 988733432, '2014-10-27', '2017-10-23');

INSERT INTO employee (ssn, fname, mname, lname, street, city, state, zip, phone, lictype, licno, sdate, edate)
VALUES (281009348, 'Josie', 'Linn', 'Michaelson', '832 Johnson Drive', 'Bakersfield', 'CA', 93309, 6613931655, 'RN', 789933728, '1994-08-16', '2003-10-15');

INSERT INTO employee (ssn, fname, mname, lname, street, city, state, zip, phone, lictype, licno, sdate, edate)
VALUES (233787766, 'Jessie', 'Adam', 'Nicholson', '3 Grange Way', 'Bakersfield', 'CA', 93304, 6618893442, 'MD', 342536266, '1990-12-30', '2017-10-29');

--insert
--patient
INSERT INTO patient ( ssn, fname, mname, lname, street, city, state, zip, phone, dob, sex, instype, language) VALUES (199999999, 'Bijan', 'M', 'Mirkazemi', 'XYZ St', 'Bakersfield', 'CA', 93312, 5555555, '1992-06-05', 'Male', 'Wellcare', 'English');
 
INSERT INTO patient ( ssn, fname, mname, lname, street, city, state, zip, phone, dob, sex, instype, language) VALUES (999999123, 'John', 'R', 'Ruiz', 'Fallview Rd', 'Bakersfield', 'CA', 93311, 5555512, '1993-02-05', 'Male', 'Cigna' , 'Spanish');
 
INSERT INTO patient ( ssn, fname, mname, lname, street, city, state, zip, phone, dob, sex, instype, language) VALUES (999991234, 'Tim', 'J', 'Jones', 'Elm St', 'Shafter', 'CA', 94081, 1234567, '1980-01-20', 'Male', 'Aetna', 'English');
 
INSERT INTO patient ( ssn, fname, mname, lname, street, city, state, zip, phone, dob, sex, instype, language) VALUES (999912345, 'Angel', 'A', 'Robles', 'A St', 'Bakersfield', 'CA', 93311, 6654536, '1950-08-30', 'Male', 'Molina', 'English');
 
INSERT INTO patient ( ssn, fname, mname, lname, street, city, state, zip, phone, dob, sex, instype, language) VALUES (999123673, 'Jane', 'M', 'Thomas', 'Waxwing Dr', 'Bakersfield', 'CA', 93309, 5555123, '1998-06-10', 'Female', 'UnitedHealth', 'English');
 
INSERT INTO patient ( ssn, fname, mname, lname, street, city, state, zip, phone, dob, sex, instype, language) VALUES (912345678, 'Dirk', 'L', 'Cutter', 'Walnut St', 'Bakersfield', 'CA', 93311, 5554444, '1990-01-01', 'Male', 'Wellcare', 'English');
 
INSERT INTO patient ( ssn, fname, mname, lname, street, city, state, zip, phone, dob, sex, instype, language) VALUES (999995467, 'Brian', 'I', 'Doe', 'Montana Pt', 'Bakersfield', 'CA', 93312, 5555555, '1972-06-04', 'Male', 'Molina', 'English');
 
INSERT INTO patient ( ssn, fname, mname, lname, street, city, state, zip, phone, dob, sex, instype, language) VALUES (999999911, 'Gabby', 'J', 'Hernandez', 'Main St', 'Wasco', 'CA', 93304, 5555333, '1985-12-15', 'Female', 'Wellpoint', 'English');
 
INSERT INTO patient ( ssn, fname, mname, lname, street, city, state, zip, phone, dob, sex, instype, language) VALUES (993499929, 'Sara', 'R', 'Willis', 'Fisk Ct', 'Delano', 'CA', 93301, 5555522, '1960-10-12', 'Female', 'Humana', 'English');
 
INSERT INTO patient ( ssn, fname, mname, lname, street, city, state, zip, phone, dob, sex, instype, language) VALUES (999991111, 'Wing', 'E', 'Wang', 'Helena Pt', 'Arvin', 'CA', 93315, 1114444, '1995-07-10', 'Female', 'Cigna', 'Spanish');
 
INSERT INTO patient ( ssn, fname, mname, lname, street, city, state, zip, phone, dob, sex, instype, language) VALUES (999992222, 'Marcus', 'J', 'Winston', 'XXX St', 'Fresno', 'CA', 93312, 5555555, '1981-10-20', 'Male', 'Wellcare', 'English');
 
INSERT INTO patient ( ssn, fname, mname, lname, street, city, state, zip, phone, dob, sex, instype, language) VALUES (999998867, 'Ariana', 'A', 'Marquez', 'ZZ St', 'Tulare', 'CA', 93312, 5555555, '1999-03-01', 'Female', 'Wellcare', 'English');
 
INSERT INTO patient ( ssn, fname, mname, lname, street, city, state, zip, phone, dob, sex, instype, language) VALUES (123456789, 'Jen', 'S', 'James', 'Y St', 'Taft', 'CA', 93312, 5555555, '1947-09-15', 'Female', 'Molina', 'English');
 
INSERT INTO patient ( ssn, fname, mname, lname, street, city, state, zip, phone, dob, sex, instype, language) VALUES (999955555, 'Tony', 'W', 'Romo', '2nd St', 'Bakersfield', 'CA', 93312, 5555555, '1955-02-19', 'Male', 'Wellpoint', 'English');

--labs
INSERT INTO labs (bun, calcium, c02, chloride, creatinine, glucose, potassium, sodium)
VALUES (6, 8.1, 35, 91, 1.2, 99, 3.8, 150);

INSERT INTO labs (bun, calcium, c02, chloride, creatinine, glucose, potassium, sodium)
VALUES (12, 10.5, 22, 106, 0.72, 74, 3.5, 142);

INSERT INTO labs (bun, calcium, c02, chloride, creatinine, glucose, potassium, sodium)
VALUES (28, 7.1, 16, 103, 1.2, 88, 5.1, 144);

INSERT INTO labs (bun, calcium, c02, chloride, creatinine, glucose, potassium, sodium)
VALUES (7, 8.8, 26, 100, 1.49, 102, 3.4, 135);

INSERT INTO labs (bun, calcium, c02, chloride, creatinine, glucose, potassium, sodium)
VALUES (11, 7.2, 20, 103, 1.35, 93, 4.4, 137);

INSERT INTO labs (bun, calcium, c02, chloride, creatinine, glucose, potassium, sodium)
VALUES (14, 8.6, 28, 90, 1.17, 75, 3.5, 149);

INSERT INTO labs (bun, calcium, c02, chloride, creatinine, glucose, potassium, sodium)
VALUES (25, 10.7, 27, 93, 1.4, 88, 3.0, 144);

INSERT INTO labs (bun, calcium, c02, chloride, creatinine, glucose, potassium, sodium)
VALUES (15, 9.3, 25, 99, 0.86, 70, 3.9, 146);

INSERT INTO labs (bun, calcium, c02, chloride, creatinine, glucose, potassium, sodium)
VALUES (16, 8.5, 27, 94, 1.41, 91, 3.4, 134);

INSERT INTO labs (bun, calcium, c02, chloride, creatinine, glucose, potassium, sodium)
VALUES (23, 10.5, 22, 103, 1.21, 102, 4.5, 139);

INSERT INTO labs (bun, calcium, c02, chloride, creatinine, glucose, potassium, sodium)
VALUES (18, 7.6, 30, 98, 1.2, 92, 3.7, 143);

INSERT INTO labs (bun, calcium, c02, chloride, creatinine, glucose, potassium, sodium)
VALUES (14, 8.3, 27, 95, 1.3, 86, 3.8, 136);

INSERT INTO labs (bun, calcium, c02, chloride, creatinine, glucose, potassium, sodium)
VALUES (18, 9.9, 27, 100, 1.23, 99, 3.4, 148);

INSERT INTO labs (bun, calcium, c02, chloride, creatinine, glucose, potassium, sodium)
VALUES (19, 9.1, 29, 103, 1.44, 93, 3.2, 129);

--prescription
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Acetaminophen', 34.0, 'Daily');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Furosemide', 300, 'With meals');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('GrowAPair', 100, 'Every Morning');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Acetaminophen', 1.0, 'Daily');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Oxycodone', 0.5, 'Q 4 Hours');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Hydrocodone', 2.5, 'Q 4 Hours');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Vancomycin Injection', 100, 'Q 12 Hours');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Oxycodone', 3.5, 'Daily');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Carvedilol Tab', 3.5, 'Q 8 Hours');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Pepto Bismol', 3.5, 'With meals');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Robotussin', 3.5, 'Q 6 Hours');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Morphine', 6, 'Q 4 Hours');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Edibles', 100, 'Q 6 Hours');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Medical Marijuana', 200, 'daily');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Morphine', 7, 'Q 6 Hours');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Carvedilol Tab', 3.0, 'Q 8 Hours');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Growapair', 125, 'Every Morning');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Albuteral', 1.0, 'Daily');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Cyanide', 0.3, 'Q 4 Hours');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Oxycodone', 2.0, 'Q 4 Hours');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Vancomycin Injection', 80, 'Q 12 Hours');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Oxycodone', 3.0, 'Daily');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Carvedilol Tab', 7.5, 'Q 8 Hours');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Pepto Bismol', 4.0, 'With meals');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Robotussin', 7.6, 'Q 6 Hours');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Morphine', 6.5, 'Q 4 Hours');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Edibles', 35, 'Q 6 Hours');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Medical Marijuana', 400, 'daily');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Acetaminophen', 36.0, 'Daily');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Furosemide', 98.2, 'With meals');
INSERT INTO prescription (medication, dosage, frequency) VALUES ('Growapair', 175, 'Every Morning');

--diagnosis
INSERT INTO diagnosis (diagnosis, status, lid, eid) VALUES ('Hepatitis A', 1, 12, 1);
INSERT INTO diagnosis (diagnosis, status, lid, eid) VALUES ('Paralyzed legs down', 2, 1, 2);
INSERT INTO diagnosis (diagnosis, status, lid, eid) VALUES ('Broken Neck', 0, 2, 2);
INSERT INTO diagnosis (diagnosis, status, lid, eid) VALUES ('Stroke', 1, 13, 7);
INSERT INTO diagnosis (diagnosis, status, lid, eid) VALUES ('Hepatitis C', 1, 7, 3);
INSERT INTO diagnosis (diagnosis, status, lid, eid) VALUES ('Hepatitis C', 0, 6, 13);
INSERT INTO diagnosis (diagnosis, status, lid, eid) VALUES ('Hepatitis B', 1, 4, 11);
INSERT INTO diagnosis (diagnosis, status, lid, eid) VALUES ('HIV; Failure to Thrive', 2, 8, 11);
INSERT INTO diagnosis (diagnosis, status, lid, eid) VALUES ('Migraine', 2, 10, 10);
INSERT INTO diagnosis (diagnosis, status, lid, eid) VALUES ('Herpes', 1, 3, 10);
INSERT INTO diagnosis (diagnosis, status, lid, eid) VALUES ('GI Bleed', 2, 9, 7);
INSERT INTO diagnosis (diagnosis, status, lid, eid) VALUES ('Broken Nose', 0, 11, 4);
INSERT INTO diagnosis (diagnosis, status, lid, eid) VALUES ('Fractured Fibula', 2, 5, 10);
INSERT INTO diagnosis (diagnosis, status, lid, eid) VALUES ('Stroke', 1, 14, 9);

--insert
--forms
INSERT INTO forms (eID, careteamid, sdate) VALUES (1, 1, '2015-10-01');
INSERT INTO forms (eID, careteamid, sdate) VALUES (4, 1, '2015-10-01');
INSERT INTO forms (eID, careteamid, sdate) VALUES (3, 1, '2015-10-01');
INSERT INTO forms (eID, careteamid, sdate) VALUES (6, 1, '2015-10-01');
INSERT INTO forms (eID, careteamid, sdate) VALUES (12, 2, '2014-11-05');
INSERT INTO forms (eID, careteamid, sdate) VALUES (9, 2, '2014-11-05');
INSERT INTO forms (eID, careteamid, sdate) VALUES (2, 2, '2014-11-05');
INSERT INTO forms (eID, careteamid, sdate) VALUES (5, 3, '2016-10-01');
INSERT INTO forms (eID, careteamid, sdate) VALUES (13, 3, '2016-10-01');
INSERT INTO forms (eID, careteamid, sdate) VALUES (12, 4, '2015-7-01');
INSERT INTO forms (eID, careteamid, sdate) VALUES (10, 4, '2015-7-01');
INSERT INTO forms (eID, careteamid, sdate) VALUES (11, 5, '2015-4-21');
INSERT INTO forms (eID, careteamid, sdate) VALUES (2, 5, '2015-4-21');
INSERT INTO forms (eID, careteamid, sdate) VALUES (5, 5, '2015-4-21');
INSERT INTO forms (eID, careteamid, sdate) VALUES (6, 6, '2012-10-15');
INSERT INTO forms (eID, careteamid, sdate) VALUES (7, 6, '2012-10-15');
INSERT INTO forms (eID, careteamid, sdate) VALUES (14, 6, '2012-10-15');
INSERT INTO forms (eID, careteamid, sdate) VALUES (12, 7, '2016-5-20');
INSERT INTO forms (eID, careteamid, sdate) VALUES (2, 7, '2016-5-20');
INSERT INTO forms (eID, careteamid, sdate) VALUES (1, 8, '2014-1-01');
INSERT INTO forms (eID, careteamid, sdate) VALUES (8, 8, '2014-1-01');
INSERT INTO forms (eID, careteamid, sdate) VALUES (3, 8, '2014-1-01');
INSERT INTO forms (eID, careteamid, sdate) VALUES (1, 9, '2015-12-11');
INSERT INTO forms (eID, careteamid, sdate) VALUES (11, 9, '2015-12-11');
INSERT INTO forms (eID, careteamid, sdate) VALUES (13, 10, '2015-10-12');
INSERT INTO forms (eID, careteamid, sdate) VALUES (3, 10, '2015-10-12');
INSERT INTO forms (eID, careteamid, sdate) VALUES (1, 10, '2015-10-12');
INSERT INTO forms (eID, careteamid, sdate) VALUES (6, 11, '2014-3-14');
INSERT INTO forms (eID, careteamid, sdate) VALUES (2, 11, '2014-3-14');
INSERT INTO forms (eID, careteamid, sdate) VALUES (8, 11, '2014-3-14');
INSERT INTO forms (eID, careteamid, sdate) VALUES (4, 12, '2017-10-20');
INSERT INTO forms (eID, careteamid, sdate) VALUES (1, 12, '2017-10-20');
INSERT INTO forms (eID, careteamid, sdate) VALUES (10, 13, '2016-4-02');
INSERT INTO forms (eID, careteamid, sdate) VALUES (5, 13, '2016-4-02');
INSERT INTO forms (eID, careteamid, sdate) VALUES (2, 13, '2016-4-02');
INSERT INTO forms (eID, careteamid, sdate) VALUES (3, 13, '2016-4-02');
INSERT INTO forms (eID, careteamid, sdate) VALUES (7, 14, '2015-6-01');
INSERT INTO forms (eID, careteamid, sdate) VALUES (6, 14, '2015-6-01');
INSERT INTO forms (eID, careteamid, sdate) VALUES (9, 15, '2014-9-22');
INSERT INTO forms (eID, careteamid, sdate) VALUES (11, 15, '2014-9-22');
INSERT INTO forms (eID, careteamid, sdate) VALUES (4, 15, '2014-9-22');
INSERT INTO forms (eID, careteamid, sdate) VALUES (3, 16, '2015-01-01');
INSERT INTO forms (eID, careteamid, sdate) VALUES (2, 16, '2015-01-01');
INSERT INTO forms (eID, careteamid, sdate) VALUES (1, 16, '2015-01-01');

--insert
--prescribes
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (1, 11, '2015-4-12', '2018-5-12');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (2, 12, '2014-1-11', '2015-2-11');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (4, 10, '2015-10-10', '2015-11-10');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (3, 11, '2015-5-01', '2018-6-01');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (5, 9, '2015-3-13', '2015-3-13');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (8, 7, '2015-9-20', '2018-9-20');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (7, 6, '2015-8-19', '2015-9-19');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (9, 7, '2015-11-17', '2018-12-17');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (6, 3, '2015-1-09', '2015-2-09');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (12, 2, '2015-2-05', '2018-3-05');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (14, 6, '2015-4-30', '2015-5-30');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (15, 1, '2015-12-28', '2015-1-28');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (16, 2, '2015-9-20', '2015-10-20');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (17, 1, '2015-2-01', '2018-3-01');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (18, 8, '2014-3-12', '2015-5-12');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (19, 12, '2016-5-12', '2016-6-12');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (20, 7, '2017-2-17', '2017-2-17');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (21, 9, '2014-4-12', '2018-5-12');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (23, 13, '2016-1-11', '2016-2-11');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (22, 11, '2016-5-30', '2015-6-30');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (24, 8, '2016-9-11', '2016-9-11');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (25, 3, '2015-11-11', '2015-11-11');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (26, 1, '2017-4-23', '2017-5-23');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (27, 6, '2014-10-20', '2018-11-20');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (28, 5, '2013-8-19', '2013-8-19');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (29, 2, '2016-9-05', '2016-10-05');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (30, 10, '2017-3-22', '2017-4-22');
INSERT INTO prescribes (prid, did, sdate, edate) VALUES (31, 14, '2015-1-01', '2015-2-01');

--insert
--seenby

INSERT INTO seenby (pid, careteamid, date) VALUES (1, 2, '2015-1-16');
INSERT INTO seenby (pid, careteamid, date) VALUES (6, 1, '2015-2-03');
INSERT INTO seenby (pid, careteamid, date) VALUES (2, 3, '2012-3-15');
INSERT INTO seenby (pid, careteamid, date) VALUES (11, 4, '2014-4-29');
INSERT INTO seenby (pid, careteamid, date) VALUES (14, 5, '2013-10-19');
INSERT INTO seenby (pid, careteamid, date) VALUES (12, 6, '2009-6-12');
INSERT INTO seenby (pid, careteamid, date) VALUES (13, 12, '2008-4-01');
INSERT INTO seenby (pid, careteamid, date) VALUES (10, 8, '2012-12-10');
INSERT INTO seenby (pid, careteamid, date) VALUES (8, 10, '2016-10-25');
INSERT INTO seenby (pid, careteamid, date) VALUES (9, 7, '2014-11-10');
INSERT INTO seenby (pid, careteamid, date) VALUES (3, 11, '2011-6-16');
INSERT INTO seenby (pid, careteamid, date) VALUES (7, 9, '2012-5-04');
INSERT INTO seenby (pid, careteamid, date) VALUES (4, 14, '2014-3-26');
INSERT INTO seenby (pid, careteamid, date) VALUES (1, 15, '2008-4-12');
INSERT INTO seenby (pid, careteamid, date) VALUES (6, 2, '2007-11-06');
INSERT INTO seenby (pid, careteamid, date) VALUES (2, 2, '2006-10-21');
INSERT INTO seenby (pid, careteamid, date) VALUES (13, 4, '2017-2-15');
INSERT INTO seenby (pid, careteamid, date) VALUES (14, 1, '2014-1-12');
INSERT INTO seenby (pid, careteamid, date) VALUES (12, 5, '2010-3-30');
INSERT INTO seenby (pid, careteamid, date) VALUES (11, 3, '2011-4-22');
INSERT INTO seenby (pid, careteamid, date) VALUES (9, 6, '2013-6-02');
INSERT INTO seenby (pid, careteamid, date) VALUES (10, 8, '2014-5-03');
INSERT INTO seenby (pid, careteamid, date) VALUES (8, 9, '2009-7-10');
INSERT INTO seenby (pid, careteamid, date) VALUES (7, 7, '2011-2-12');
INSERT INTO seenby (pid, careteamid, date) VALUES (6, 10, '2012-11-24');
INSERT INTO seenby (pid, careteamid, date) VALUES (4, 12, '2013-12-20');
INSERT INTO seenby (pid, careteamid, date) VALUES (5, 16, '2017-4-05');
INSERT INTO seenby (pid, careteamid, date) VALUES (2, 14, '2016-2-10');
INSERT INTO seenby (pid, careteamid, date) VALUES (3, 11, '2015-5-20');
INSERT INTO seenby (pid, careteamid, date) VALUES (1, 13, '2014-2-11');

--cases
INSERT INTO cases (bpsys, bpdia, hrate, rrate, vdate, pid, did, eid)
VALUES (93, 48, 111, 71, '2011-02-09', 3, 14, 14);

INSERT INTO cases (bpsys, bpdia, hrate, rrate, vdate, pid, did, eid)
VALUES (133, 94, 55, 94, '1999-08-02', 2, 7, 1);

INSERT INTO cases (bpsys, bpdia, hrate, rrate, vdate, pid, did, eid)
VALUES (175, 94, 167, 85, '2010-03-01', 1, 3, 3);

INSERT INTO cases (bpsys, bpdia, hrate, rrate, vdate, pid, did, eid)
VALUES (132, 45, 59, 86, '2011-03-28', 10, 2, 5);

INSERT INTO cases (bpsys, bpdia, hrate, rrate, vdate, pid, did, eid)
VALUES (121, 47, 185, 77, '1999-03-10', 8, 4, 6);

INSERT INTO cases (bpsys, bpdia, hrate, rrate, vdate, pid, did, eid)
VALUES (185, 66, 44, 98, '1996-11-13', 3, 10, 14);

INSERT INTO cases (bpsys, bpdia, hrate, rrate, vdate, pid, did, eid)
VALUES (151, 63, 182, 81, '2005-12-20', 13, 6, 5);

INSERT INTO cases (bpsys, bpdia, hrate, rrate, vdate, pid, did, eid)
VALUES (105, 68, 136, 95, '2016-12-12', 14, 5, 8);

INSERT INTO cases (bpsys, bpdia, hrate, rrate, vdate, pid, did, eid)
VALUES (90, 58, 80, 79, '2004-04-23', 3, 8, 8);

INSERT INTO cases (bpsys, bpdia, hrate, rrate, vdate, pid, did, eid)
VALUES (112, 67, 132, 92, '2000-06-16', 5, 13, 3);

INSERT INTO cases (bpsys, bpdia, hrate, rrate, vdate, pid, did, eid)
VALUES (120, 80, 80, 93, '2009-06-16', 9, 11, 5);

INSERT INTO cases (bpsys, bpdia, hrate, rrate, vdate, pid, did, eid)
VALUES (105, 67, 68, 99, '1999-11-14', 11, 9, 8);

INSERT INTO cases (bpsys, bpdia, hrate, rrate, vdate, pid, did, eid)
VALUES (137, 75, 76, 98, '2003-01-30', 6, 12, 8);

INSERT INTO cases (bpsys, bpdia, hrate, rrate, vdate, pid, did, eid)
VALUES (119, 83, 92, 94, '2017-10-31', 7, 1, 8);






