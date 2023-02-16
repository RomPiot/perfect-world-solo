--102
lua_glb = {}

function lua_glb:check_md5(mapid)
	local maps = {}


		local files_to_check = maps[string.lower(mapid)]
	if files_to_check == nil then
		return true
	end

	local f, m
	for f,m in pairs(files_to_check) do
		md5 = GlobalApi.lua_glb_CalcFileMd5(f)
		if string.lower(m) ~= string.lower(md5) then
			return false
		end	 
	end
	
 	return true
end