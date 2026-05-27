@echo off

curl.exe ^"https://6269176.techinfojoliette.ca:2083/cpsess5681528945/execute/VersionControlDeployment/create^" ^
  -X POST ^
  -H ^"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0^" ^
  -H ^"Accept: */*^" ^
  -H ^"Accept-Language: en-CA,en-US;q=0.9,en;q=0.8^" ^
  -H ^"Accept-Encoding: gzip, deflate, br, zstd^" ^
  -H ^"Referer: https://6269176.techinfojoliette.ca:2083/^" ^
  -H ^"Content-Type: application/x-www-form-urlencoded; charset=UTF-8^" ^
  -H ^"X-Requested-With: XMLHttpRequest^" ^
  -H ^"Origin: https://6269176.techinfojoliette.ca:2083^" ^
  -H ^"Connection: keep-alive^" ^
  -H ^"Cookie: PHPSESSID=8d3e6a278d20198be8a50935b0b61784; cpsession=u6269176^%%^3aQlaHLcGJCBEdfGFV^%%^2c491d87fbab4132bbab2ff4ed06e698dc; timezone=America/New_York^" ^
  -H ^"Sec-Fetch-Dest: empty^" ^
  -H ^"Sec-Fetch-Mode: cors^" ^
  -H ^"Sec-Fetch-Site: same-origin^" ^
  -H ^"Priority: u=0^" ^
  --data-raw ^"repository_root=^%%^2Fhome^%%^2Fu6269176^%%^2Frepositories^%%^2Ftp2_web2^"
  