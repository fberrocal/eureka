-- ==========================================================================================================================
-- Stored Procedure que genera los campos del reporte utilizando un pivot
-- Versión 2
-- Ing. FMBM [30-04-2019]: Se cambia el nombre del campo fecha ya que está en conflicto con los nombres de campos de las HC
-- ==========================================================================================================================

-- ==========================================================================================================================
-- Chage Log:
-- Se optimiza para ingresar más datos del Afiliado

-- SELECT TOP 100 TIPODISCAPACIDAD,IDOCUPACION,ESTADO_CIVIL,IDESCOLARIDAD FROM AFI
-- select * from tgen where tabla='AFI' AND CAMPO='TIPODISCAPACIDAD';
-- select * from OCU where OCUPACION='999';
-- select * from tgen where tabla='AFI' AND CAMPO='ESTADO_CIVIL';
-- select * from tgen where tabla='AFI' AND CAMPO='IDESCOLARIDAD';

-- [30.07.2020 - Dev. fberrocalm] Se agregan los campos de diagnóstico al reporte

-- ==========================================================================================================================
ALTER PROCEDURE [dbo].[spV_HCA_Pivot_v2] 
	@FechaIni datetime,
	@FechaFin datetime,
	@ClasePlantilla varchar(8),
	@IDSEDE varchar(4),
	@Columnas varchar(max)

AS
    SET NOCOUNT ON;
	
	-- Si el parámetro @Claseplantilla viene vacío (Se cancela el proceso)
	if @Claseplantilla=''
	begin 
		raiserror ('Falta el parámetro ClasePlantilla',16,1)
		return
	end
	
	DECLARE 
		@Columnas1 VARCHAR(max),
		@Columnas2 VARCHAR(max)
	
	-- Si el parámetro @Columnas esta vacío (se leen nuevamente de la base de datos) 	
	if @Columnas=''
	begin
		SELECT @Columnas1 =	COALESCE (@Columnas1 + ', [' + ltrim(CAMPO) + '] ','[' +  ltrim(CAMPO)+ ']')
		FROM MPLD 
		WHERE CLASEPLANTILLA=@ClasePlantilla
		GROUP BY Secuencia,CLASEPLANTILLA,campo
		ORDER by Secuencia
		set @Columnas = @Columnas1;
	end
	
	print @Columnas1;
	
	set @Columnas2=replace(replace(@Columnas,'[',''''),']','''');  -- Se reemplaza el corchete ([) por las comillas dobles ""
	
	DECLARE @query NVARCHAR(max)
	SET @query = 
	'
	declare @HCA table(CONSECUTIVO varchar(13), IDSEDE VARCHAR(4), 
					FECHA_HC DATETIME,IDMEDICO VARCHAR(12), IDAFILIADO VARCHAR(20), TIPODX VARCHAR(10), IDDX VARCHAR(10), DX1 VARCHAR(10), DX2 VARCHAR(10), DX3 VARCHAR(10));
declare @TGEN table(CODIGO varchar(20), DESCRIPCION VARCHAR(512));
declare @TGEN2 table(CODIGO varchar(20), DESCRIPCION VARCHAR(512));
declare @TGEN3 table(CODIGO varchar(20), DESCRIPCION VARCHAR(512));
declare @TGEN4 table(CODIGO varchar(20), DESCRIPCION VARCHAR(512));
declare @OCU table(OCUPACION varchar(5), DESCRIPCION VARCHAR(100));

INSERT INTO @HCA
SELECT CONSECUTIVO,IDSEDE,FECHA,IDMEDICO,IDAFILIADO,TIPODX,IDDX,DX1,DX2,DX3
FROM HCA WITH (NOLOCK)
WHERE
CLASEPLANTILLA=@ClasePlantilla  
AND FECHA BETWEEN @FechaIni and @FechaFin
AND estado<>''Anulada'' 
AND idsede=CASE WHEN COALESCE(@IDSEDE,'''')='''' THEN HCA.IDSEDE ELSE @IDSEDE END;

INSERT INTO @TGEN
SELECT CODIGO,DESCRIPCION
FROM TGEN 
WHERE TABLA=''AFI'' AND CAMPO=''GRUPOETNICO'';

INSERT INTO @TGEN2
SELECT CODIGO,DESCRIPCION
FROM TGEN 
WHERE TABLA=''AFI'' AND CAMPO=''TIPODISCAPACIDAD'';

INSERT INTO @TGEN3
SELECT CODIGO,DESCRIPCION
FROM TGEN 
WHERE TABLA=''AFI'' AND CAMPO=''ESTADO_CIVIL'';

INSERT INTO @TGEN4
SELECT CODIGO,DESCRIPCION
FROM TGEN 
WHERE TABLA=''AFI'' AND CAMPO=''IDESCOLARIDAD'';

INSERT INTO @OCU
SELECT OCUPACION,DESCRIPCION
FROM OCU;
	SEDE
	SELECT * FROM (Select AFI.IDADMINISTRADORA,TER.RAZONSOCIAL,AFI.TIPO_DOC, AFI.IDAFILIADO, COALESCE(AFI.PAPELLIDO,'''') AS PAPELLIDO, COALESCE(AFI.SAPELLIDO,'''') AS SAPELLIDO, COALESCE(AFI.PNOMBRE,'''') AS PNOMBRE, COALESCE(AFI.SNOMBRE,'''') AS SNOMBRE,  CONVERT(VARCHAR(12),AFI.FNACIMIENTO,103) AS FNACIMIENTO, 	dbo.fna_EdadenAnos(AFI.FNACIMIENTO,HCA.FECHA_HC) AS EDAD, AFI.SEXO,	AFI.DIRECCION,AFI.TELEFONORES,COALESCE(TGEN.DESCRIPCION,'''') AS GRUPOETNICO, COALESCE(TGEN2.DESCRIPCION,'''') AS TIPODISCAPACIDAD, COALESCE(TGEN3.DESCRIPCION,'''') AS ESTADOCIVIL, COALESCE(TGEN4.DESCRIPCION,''NINGUNO'') AS IDESCOLARIDAD, COALESCE(OCU.DESCRIPCION,'''') AS OCUPACIONC, HCAD.CONSECUTIVO, HCA.IDSEDE,SED.DESCRIPCION AS SEDECAB, HCA.FECHA_HC, HCAD.CLASEPLANTILLA, MED.TIPO_USUARIO,  MED.IDEMEDICA AS ESPECIALIDADM,
	  (CASE HCAD.TIPOCAMPO 
			WHEN ''Alfanumerico'' THEN REPLACE(REPLACE(REPLACE(CONVERT(NVARCHAR(MAX),COALESCE(HCAD.ALFANUMERICO,'''')),CHAR(13),''''),CHAR(9),''''),CHAR(10),'''')
			WHEN ''Memo'' THEN REPLACE(REPLACE(REPLACE(CONVERT(NVARCHAR(MAX),COALESCE(HCAD.MEMO,'''')),CHAR(13),''''),CHAR(9),''''),CHAR(10),'''') 
			WHEN ''Fecha'' THEN CONVERT(NVARCHAR(MAX),COALESCE(CONVERT(VARCHAR(12),HCAD.FECHA,103),'''')) 
			WHEN ''Lista'' THEN CONVERT(NVARCHAR(MAX),COALESCE(HCAD.ALFANUMERICO,'''')+COALESCE(HCAD.LISTA,'''')) 	  
			WHEN ''MultiCheck'' THEN CONVERT(NVARCHAR(MAX),dbo.Fn_ValoresHCADL(HCA.CONSECUTIVO,HCAD.SECUENCIA)) 
			WHEN ''TGEN'' THEN CONVERT(NVARCHAR(MAX),dbo.Fn_DescripcionTGEN(@ClasePlantilla,HCAD.CAMPO,HCAD.ALFANUMERICO)) 
		END) AS VARIABLE, HCAD.CAMPO, HCA.IDMEDICO, MED.NOMBRE AS  MEDICO, HCA.TIPODX, HCA.IDDX, HCA.DX1, HCA.DX2, HCA.DX3
	/*FROM HCAD 
	INNER JOIN HCA ON HCAD.CONSECUTIVO=HCA.CONSECUTIVO 
	INNER JOIN AFI ON AFI.IDAFILIADO=HCA.IDAFILIADO 
	INNER JOIN TER ON TER.IDTERCERO=AFI.IDADMINISTRADORA 
	INNER JOIN MED ON MED.IDMEDICO=HCA.IDMEDICO 
	LEFT JOIN SED ON SED.IDSEDE=HCA.IDSEDE 
	LEFT JOIN TGEN ON AFI.GRUPOETNICO=TGEN.CODIGO AND TGEN.TABLA=''AFI'' AND TGEN.CAMPO=''GRUPOETNICO'' 
	*/
	FROM HCAD WITH (INDEX(HCADCONSECUTIVO),NOLOCK) 
	INNER JOIN @HCA HCA ON HCAD.CONSECUTIVO=HCA.CONSECUTIVO 
	INNER JOIN AFI ON AFI.IDAFILIADO=HCA.IDAFILIADO 
	INNER JOIN TER ON TER.IDTERCERO=AFI.IDADMINISTRADORA 
	INNER JOIN MED ON MED.IDMEDICO=HCA.IDMEDICO 
	LEFT JOIN SED ON SED.IDSEDE=HCA.IDSEDE 
	LEFT JOIN @TGEN TGEN ON AFI.GRUPOETNICO=TGEN.CODIGO	
	LEFT JOIN @TGEN2 TGEN2 ON AFI.TIPODISCAPACIDAD=TGEN2.CODIGO
	LEFT JOIN @TGEN3 TGEN3 ON AFI.ESTADO_CIVIL=TGEN3.CODIGO
	LEFT JOIN @TGEN4 TGEN4 ON AFI.IDESCOLARIDAD=TGEN4.CODIGO
	LEFT JOIN @OCU OCU ON AFI.IDOCUPACION=OCU.OCUPACION
	WHERE  
		HCAD.CAMPO IN ('+@Columnas2+') 
	) PIV PIVOT(MAX(VARIABLE) FOR CAMPO IN ('+@Columnas+')) X;'

	print @query
	--set transaction isolation level read uncommitted; 
EXECUTE sp_executesql @query, N'@IDSEDE varchar(2), @FechaIni DATETIME, @FechaFin DATETIME, @ClasePlantilla varchar(8)',@IDSEDE, @FechaIni, @FechaFin, @ClasePlantilla 
/***********************************************************************************************************************************/
