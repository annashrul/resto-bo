
<?php
/*$get_data = $this->m_crud->read_data("bayar_hutang", "Tgl_byr", null, null, "Tgl_byr, nota", 0, 0, "COUNT(nota) > 1");

foreach ($get_data as $row) {
    $this->db->query("
    DECLARE @x INT = 1
    DECLARE @i INT
    DECLARE @d INT = (SELECT COUNT(*) from DB_GIANDY_TEST.dbo.bayar_hutang WHERE Tgl_byr='".$row['Tgl_byr']."')
    WHILE (@x < @d)
    BEGIN
    SET @i = (SELECT MAX(CONVERT(INTEGER, SUBSTRING(nota, 10, 4))) + 1 FROM DB_GIANDY_TEST.dbo.bayar_hutang WHERE Tgl_byr = '".$row['Tgl_byr']."')
    UPDATE TOP(@d-@x) DB_GIANDY_TEST.dbo.bayar_hutang set nota = ('BH-'+SUBSTRING(CONVERT(VARCHAR,Tgl_byr,120),3,2)+SUBSTRING(CONVERT(VARCHAR,Tgl_byr,120),6,2)+SUBSTRING(CONVERT(VARCHAR,Tgl_byr,120),9,2)+RIGHT('000'+(SELECT '000'+CONVERT(VARCHAR, @i)), 4)+'-A') WHERE Tgl_byr = '".$row['Tgl_byr']."'
    SET @x = @x + 1
    END
    ");
}*/

/*$get_data = $this->m_crud->read_data("bayar_piutang", "Tgl_byr", null, null, "Tgl_byr, nota", 0, 0, "COUNT(nota) > 1");

foreach ($get_data as $row) {
    $this->db->query("
    DECLARE @x INT = 1
    DECLARE @i INT
    DECLARE @d INT = (SELECT COUNT(*) from DB_GIANDY_TEST.dbo.bayar_piutang WHERE Tgl_byr='".$row['Tgl_byr']."')
    WHILE (@x < @d)
    BEGIN
    SET @i = (SELECT MAX(CONVERT(INTEGER, SUBSTRING(nota, 10, 4))) + 1 FROM DB_GIANDY_TEST.dbo.bayar_piutang WHERE Tgl_byr = '".$row['Tgl_byr']."')
    UPDATE TOP(@d-@x) DB_GIANDY_TEST.dbo.bayar_piutang set nota = ('BP-'+SUBSTRING(CONVERT(VARCHAR,Tgl_byr,120),3,2)+SUBSTRING(CONVERT(VARCHAR,Tgl_byr,120),6,2)+SUBSTRING(CONVERT(VARCHAR,Tgl_byr,120),9,2)+RIGHT('000'+(SELECT '000'+CONVERT(VARCHAR, @i)), 4)+'-A') WHERE Tgl_byr = '".$row['Tgl_byr']."'
    SET @x = @x + 1
    END
    ");
}*/
?>