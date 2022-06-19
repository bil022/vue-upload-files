#!/usr/bin/perl

sub byNR { 
  die $a unless $a=~/^(\S+)\.(\d+)$/; $fa=$1; $na=$2;
  die $b unless $b=~/^(\S+)\.(\d+)$/; $fb=$1; $nb=$2;
  die unless $fa eq $fb;
  return $nb<=>$na;
}

while (<>) { chomp(); push(@files, $_); }
print join(" ", sort byNR @files);
