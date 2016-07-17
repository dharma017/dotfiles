function git-conflicts-ours
	grep -lr '<<<<<<<' . | xargs git checkout --ours $argv;
end
