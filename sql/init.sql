USE tasklogger;

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    priority ENUM('basse', 'moyenne', 'haute') DEFAULT 'moyenne',
    status ENUM('todo', 'in_progress', 'done') DEFAULT 'todo',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS SystemEvents (
    ID int unsigned NOT NULL auto_increment,
    CustomerID bigint,
    ReceivedAt datetime NULL,
    DeviceReportedTime datetime NULL,
    Facility smallint NULL,
    Priority smallint NULL,
    FromHost varchar(60) NULL,
    Message text,
    NTSeverity int NULL,
    Importance int NULL,
    EventSource varchar(60),
    EventUser varchar(60) NULL,
    EventCategory int NULL,
    EventID int NULL,
    EventBinaryData text,
    MaxAvailable int NULL,
    CurrUsage int NULL,
    MinUsage int NULL,
    MaxUsage int NULL,
    InfoUnitID int NULL,
    SysLogTag varchar(60),
    EventLogType varchar(60),
    GenericFileName varchar(60),
    SystemID int NULL,
    PRIMARY KEY (ID)
);
