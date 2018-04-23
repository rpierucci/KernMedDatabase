--delete patient function
CREATE OR REPLACE FUNCTION drop_pat(pat_id integer)
	RETURNS void AS $$
		BEGIN
			DELETE FROM patient p WHERE p.pid = pat_id;
		END;
	$$ LANGUAGE 'plpgsql';


--insert case function
CREATE OR REPLACE FUNCTION insert_case(cid integer, bpsys integer, bpdia integer, hrate integer, rrate integer, vdate date, pid integer, did integer, eid integer)
	RETURNS void AS $$
		BEGIN
			INSERT INTO cases VALUES (cid, bpsys, bpdia, hrate, rrate, vdate, pid, did, eid);
		END
	$$ LANGUAGE 'plpgsql';


--update case function

CREATE OR REPLACE FUNCTION update_case(cidi integer, bpsysi integer, bpdiai integer, hratei integer, rratei integer, vdatei date, eidi integer)
	RETURNS void AS $$
		BEGIN
			UPDATE cases 
			SET bpsys = bpsysi, bpdia = bpdiai, hrate = hratei, rrate = rratei, vdate = vdatei, eid = eidi
			WHERE cid = cidi;
		END
	$$ LANGUAGE 'plpgsql';

--triggers

--makes sure when inserting new patient names are capitalized properly

CREATE FUNCTION normalize_input() RETURNS TRIGGER AS                         
 $$ BEGIN
	NEW.fname := initcap( NEW.fname);
	NEW.mname := initcap( NEW.mname);
	NEW.lname := initcap( NEW.lname);
	NEW.city := initcap( NEW.city);
	NEW.sex := initcap( NEW.sex);
	NEW.instype := initcap ( NEW.instype);
	NEW.language := initcap (NEW.language);
	RETURN NEW;
END;
$$ language plpgsql;

CREATE TRIGGER normalize_input_trg                                            
BEFORE INSERT OR UPDATE
ON patient
FOR EACH ROW
EXECUTE PROCEDURE normalize_input();


--cascade deletes prescribes when deleting patient

CREATE OR REPLACE FUNCTION cascade_prescription() RETURNS trigger AS 
$$ BEGIN 
	DELETE FROM prescribes 
	WHERE prid = OLD.preid; RETURN OLD; 
END; 
$$ language plpgsql;

CREATE TRIGGER cascade_pre_trg 
BEFORE DELETE 
ON prescription 
FOR EACH ROW 
EXECUTE PROCEDURE cascade_prescription();

--when inserting new diagnosis creates a blank lab that is connected to it via labid

CREATE OR REPLACE FUNCTION insert_diagnosis_lab() RETURNS trigger AS
$$ BEGIN 
	INSERT INTO labs (labid, bun, calcium, c02, chloride, creatinine, glucose, potassium, sodium)
	VALUES (NEW.lid, 0, 0, 0, 0, 0, 0, 0, 0); RETURN NEW.lid;
END;
$$ language plpgsql;

CREATE TRIGGER insert_diag_lab_trg
BEFORE INSERT
ON diagnosis
FOR EACH ROW
EXECUTE PROCEDURE insert_diagnosis_lab();

--views
CREATE VIEW nursecase AS
SELECT * FROM employee NATURAL JOIN cases;

CREATE VIEW dlabs AS
SELECT did, diagnosis, status, bun, calcium, c02, chloride, creatinine, glucose, potassium, sodium
FROM diagnosis FULL OUTER JOIN labs ON diagnosis.lid = labs.labid;

CREATE VIEW currentdoctors AS
SELECT * FROM employee WHERE lictype='MD' AND edate IS NULL;
