function git-conflicts
	grep -lr '<<<<<<<' . $argv;
end
