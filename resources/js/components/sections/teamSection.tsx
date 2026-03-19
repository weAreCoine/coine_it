import BordersDecorations from '@/components/bordersDecorations';
import TeamMemberCard from '@/components/teamMemberCard';
import type { TeamMember } from '@/types/dto/sections';

type TeamSectionProps = {
    kicker: string;
    title: string;
    titleId?: string;
    subtitle: string;
    members: TeamMember[];
};

export default function TeamSection({ kicker, title, titleId, subtitle, members }: TeamSectionProps) {
    return (
        <div className="container my-24">
            <div className="text-center">
                <p className="kicker">{kicker}</p>
                <h2 id={titleId} className="section__title">
                    {title}
                </h2>
                <p className="mx-auto max-w-lg text-balance">{subtitle}</p>
            </div>
            <div className="relative mt-8 grid gap-px bg-mercury-200 p-px lg:grid-cols-2">
                <BordersDecorations />
                {members.map((member) => (
                    <TeamMemberCard key={member.name} member={member} />
                ))}
            </div>
        </div>
    );
}
