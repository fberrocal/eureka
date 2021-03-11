-- ==========================================================================================================================
-- Stored Procedure que genera los campos del reporte utilizando un pivot
-- Versión 2
-- Ing. FMBM [30-04-2019]: Se cambia el nombre del campo fecha ya que está en conflicto con los nombres de campos de las HC
-- ==========================================================================================================================
CREATE PROCEDURE [dbo].[spV_HCA_Pivot] 
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
					FECHA_HC DATETIME,IDMEDICO VARCHAR(12), IDAFILIADO VARCHAR(20));
declare @TGEN table(CODIGO varchar(20), DESCRIPCION VARCHAR(512));

INSERT INTO @HCA
SELECT CONSECUTIVO,IDSEDE,FECHA,IDMEDICO,IDAFILIADO
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


	SELECT * FROM (Select AFI.IDADMINISTRADORA,TER.RAZONSOCIAL,AFI.TIPO_DOC, AFI.IDAFILIADO, COALESCE(AFI.PAPELLIDO,'''') AS PAPELLIDO, COALESCE(AFI.SAPELLIDO,'''') AS SAPELLIDO, COALESCE(AFI.PNOMBRE,'''') AS PNOMBRE, COALESCE(AFI.SNOMBRE,'''') AS SNOMBRE,  CONVERT(VARCHAR(12),AFI.FNACIMIENTO,103) AS FNACIMIENTO, 	dbo.fna_EdadenAnos(AFI.FNACIMIENTO,HCA.FECHA_HC) AS EDAD, AFI.SEXO,	AFI.DIRECCION,AFI.TELEFONORES,COALESCE(TGEN.DESCRIPCION,'''') AS GRUPOETNICO, HCAD.CONSECUTIVO, HCA.IDSEDE,SED.DESCRIPCION AS SEDE, HCA.FECHA_HC, HCAD.CLASEPLANTILLA, MED.TIPO_USUARIO,  MED.IDEMEDICA,
	  (CASE HCAD.TIPOCAMPO 
			WHEN ''Alfanumerico'' THEN REPLACE(REPLACE(REPLACE(CONVERT(NVARCHAR(MAX),COALESCE(HCAD.ALFANUMERICO,'''')),CHAR(13),''''),CHAR(9),''''),CHAR(10),'''')
			WHEN ''Memo'' THEN REPLACE(REPLACE(REPLACE(CONVERT(NVARCHAR(MAX),COALESCE(HCAD.MEMO,'''')),CHAR(13),''''),CHAR(9),''''),CHAR(10),'''') 
			WHEN ''Fecha'' THEN CONVERT(NVARCHAR(MAX),COALESCE(CONVERT(VARCHAR(12),HCAD.FECHA,103),'''')) 
			WHEN ''Lista'' THEN CONVERT(NVARCHAR(MAX),COALESCE(HCAD.ALFANUMERICO,'''')+COALESCE(HCAD.LISTA,'''')) 	  
			WHEN ''MultiCheck'' THEN CONVERT(NVARCHAR(MAX),dbo.Fn_ValoresHCADL(HCA.CONSECUTIVO,HCAD.SECUENCIA)) 
			WHEN ''TGEN'' THEN CONVERT(NVARCHAR(MAX),dbo.Fn_DescripcionTGEN(@ClasePlantilla,HCAD.CAMPO,HCAD.ALFANUMERICO)) 
		END) AS VARIABLE, HCAD.CAMPO, HCA.IDMEDICO, MED.NOMBRE AS  MEDICO 
		
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

	WHERE  
		HCAD.CAMPO IN ('+@Columnas2+') 
	) PIV PIVOT(MAX(VARIABLE) FOR CAMPO IN ('+@Columnas+')) X;'

	print @query
	--set transaction isolation level read uncommitted; 
EXECUTE sp_executesql @query, N'@IDSEDE varchar(2), @FechaIni DATETIME, @FechaFin DATETIME, @ClasePlantilla varchar(8)',@IDSEDE, @FechaIni, @FechaFin, @ClasePlantilla 
/***********************************************************************************************************************************/


GO

